<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalAudition;
use App\Models\AnimalAuditionApplication;
use App\Models\AnimalProfile;
use App\Services\FCMService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Animal Audition feature — producers post a casting call describing the
 * animal they need, auditioners apply with one or more of their own
 * reusable Animal Profiles, producers shortlist and (via the existing
 * Firestore chat flow, unchanged here) start a conversation.
 *
 * Deliberately NOT extending App\Http\Controllers\ApiController — see the
 * doc comment on AuditionInviteController for why (its __destruct() calls
 * a broken jsonView()). This mirrors that controller's approach: extend
 * the plain Controller base and re-declare validationHandle /
 * sendUserNotification / saveNotification locally.
 *
 * Same trust model as the rest of api/*: no auth middleware, endpoints
 * trust the client-supplied user_id. Not introducing real auth here per
 * CLAUDE.md guidance — flagged as existing, not new, risk.
 */
class AnimalAuditionController extends Controller
{
    function validationHandle($validation)
    {
        foreach ($validation->getMessages() as $field_name => $messages) {
            if (!isset($firstError)) {
                $firstError = $messages[0];
                $error[$field_name] = $messages[0];
            }
        }
        return $firstError;
    }

    function sendUserNotification($data)
    {
        if (empty($data['fcmToken'])) {
            Log::warning('AnimalAuditionController::sendUserNotification skipped — no device_token on file', [
                'title' => $data['title'] ?? null,
                'customData' => $data['customData'] ?? null,
            ]);
            return;
        }
        try {
            $fcmService = new FCMService();
            $result = $fcmService->sendNotification(
                $data['fcmToken'],
                $data['title'],
                $data['message'],
                $data['customData'] ?? []
            );
            if (empty($result['success'])) {
                Log::error('AnimalAuditionController: push notification failed', [
                    'title' => $data['title'] ?? null,
                    'customData' => $data['customData'] ?? null,
                    'error' => $result['error'] ?? 'unknown',
                ]);
            } else {
                Log::info('AnimalAuditionController: push notification sent', [
                    'title' => $data['title'] ?? null,
                    'customData' => $data['customData'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('AnimalAuditionController: push notification threw', [
                'title' => $data['title'] ?? null,
                'customData' => $data['customData'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    function saveNotification($data)
    {
        if (empty($data['user_id'])) {
            return false;
        }
        DB::table('notifications')->insert([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'body' => $data['message'],
            'type' => $data['type'] ?? 'message',
            'ref_id' => $data['ref_id'] ?? null,
            'is_read' => false,
            'custom_data' => $data['custom_data'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * now() > expire_date => expired. Opportunistically persists the flip
     * (status -> 0) so the DB stays close to correct even if the
     * expire-animal-auditions cron never runs — mirrors
     * AuditionInviteController::effectiveStatus.
     */
    private function effectiveAuditionStatus($row)
    {
        if ((string) $row->status === '1' && !empty($row->expire_date)) {
            try {
                if (Carbon::parse($row->expire_date)->isPast()) {
                    DB::table('animal_auditions')
                        ->where('id', $row->id)
                        ->where('status', 1)
                        ->update(['status' => 0]);
                    return 'expired';
                }
            } catch (\Exception $e) {
                // unparsable date — fall through, treat as not expired
            }
        }
        return ((string) $row->status === '1') ? 'active' : 'inactive';
    }

    /**
     * Moves uploaded files under public/assets/<folder>/, same idiom as
     * PostauditionController's photography image handling (stores the
     * 'public/...'-prefixed relative path on the row, for consistency with
     * how the rest of the app serves images). Returns the stored paths.
     */
    private function storeUploadedPhotos(Request $request, $fileField, $folder)
    {
        $stored = [];
        if ($request->hasFile($fileField)) {
            $files = $request->file($fileField);
            $files = is_array($files) ? $files : [$files];
            foreach ($files as $key => $fileVal) {
                if (!$fileVal) {
                    continue;
                }
                $name = 'public/assets/' . $folder . '/' . time() . '_' . $key . '_' . uniqid() . '_' . str_replace(" ", "_", $fileVal->getClientOriginalName());
                $fileVal->move(public_path() . '/assets/' . $folder . '/', $name);
                $stored[] = $name;
            }
        }
        return $stored;
    }

    /**
     * Form-data for the producer's create-post form and the auditioner's
     * create-profile form: species with nested breeds, plus the static
     * enum options both forms share.
     */
    public function formData(Request $request)
    {
        $species = DB::table('animal_species')->where('status', 1)->orderBy('name')->get();
        foreach ($species as $s) {
            $s->breeds = DB::table('animal_breeds')
                ->where('animal_species_id', $s->id)
                ->where('status', 1)
                ->orderBy('name')
                ->get();
        }

        return response()->json([
            'status' => "true",
            'message' => "Animal audition form data",
            'data' => [
                'species' => $species,
                'age_range' => ['puppy_kitten', 'juvenile', 'adult', 'senior'],
                'gender' => ['male', 'female', 'unknown'],
                'size' => ['small', 'medium', 'large'],
            ],
        ]);
    }

    private function animalFieldRules()
    {
        return [
            'animal_species_id' => 'nullable|exists:animal_species,id',
            'animal_breed_id' => 'nullable|exists:animal_breeds,id',
            'age_range' => 'nullable|string',
            'gender' => 'nullable|string',
            'size' => 'nullable|string',
            'color_markings' => 'nullable|string',
            'temperament' => 'nullable|string',
        ];
    }

    /* ------------------------------------------------------------------ *
     *  Producer: animal audition posts
     * ------------------------------------------------------------------ */

    public function addAnimalAudition(Request $request)
    {
        $rules = array_merge([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'audition_date' => 'nullable|date',
            'audition_time' => 'nullable',
            'compensation' => 'nullable',
            'compensation_description' => 'nullable|string',
            'production_website' => 'nullable|string',
            'device_type' => 'required|string',
        ], $this->animalFieldRules());

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $expireDate = null;
        if (!empty($request->audition_date)) {
            $expireDate = trim($request->audition_date . ' ' . ($request->audition_time ?? '00:00:00'));
        }

        $audition = AnimalAudition::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'audition_date' => $request->audition_date,
            'audition_time' => $request->audition_time,
            'expire_date' => $expireDate,
            'compensation' => $request->compensation ? 1 : 0,
            'compensation_description' => $request->compensation_description,
            'production_website' => $request->production_website,
            'animal_species_id' => $request->animal_species_id ?: null,
            'animal_breed_id' => $request->animal_breed_id ?: null,
            'age_range' => $request->age_range,
            'gender' => $request->gender,
            'size' => $request->size,
            'color_markings' => $request->color_markings,
            'temperament' => $request->temperament,
            'status' => 1,
        ]);

        $photos = $this->storeUploadedPhotos($request, 'photos', 'animal-audition');
        foreach ($photos as $img) {
            DB::table('animal_audition_photos')->insert([
                'animal_audition_id' => $audition->id,
                'image' => $img,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['status' => "true", 'message' => "Animal audition posted successfully", 'data' => $audition]);
    }

    public function updateAnimalAudition(Request $request)
    {
        $rules = array_merge([
            'id' => 'required|exists:animal_auditions,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'device_type' => 'required|string',
        ], $this->animalFieldRules());

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $audition = AnimalAudition::find($request->id);
        if ((string) $audition->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "You do not own this post"]);
        }

        $expireDate = $audition->expire_date;
        if (!empty($request->audition_date)) {
            $expireDate = trim($request->audition_date . ' ' . ($request->audition_time ?? '00:00:00'));
        }

        $audition->title = $request->title;
        $audition->description = $request->description ?? $audition->description;
        $audition->location = $request->location ?? $audition->location;
        $audition->audition_date = $request->audition_date ?? $audition->audition_date;
        $audition->audition_time = $request->audition_time ?? $audition->audition_time;
        $audition->expire_date = $expireDate;
        if ($request->has('compensation')) {
            $audition->compensation = $request->compensation ? 1 : 0;
        }
        $audition->compensation_description = $request->compensation_description ?? $audition->compensation_description;
        $audition->production_website = $request->production_website ?? $audition->production_website;
        $audition->animal_species_id = $request->animal_species_id ?: $audition->animal_species_id;
        $audition->animal_breed_id = $request->animal_breed_id ?: $audition->animal_breed_id;
        $audition->age_range = $request->age_range ?? $audition->age_range;
        $audition->gender = $request->gender ?? $audition->gender;
        $audition->size = $request->size ?? $audition->size;
        $audition->color_markings = $request->color_markings ?? $audition->color_markings;
        $audition->temperament = $request->temperament ?? $audition->temperament;
        if ($request->has('status')) {
            $audition->status = $request->status ? 1 : 0;
        }
        $audition->save();

        // Replace photos only when new ones are actually uploaded — leaves
        // existing photos untouched otherwise (mirrors update-photography's
        // "replace all" behaviour, but only when photos are actually sent).
        if ($request->hasFile('photos')) {
            DB::table('animal_audition_photos')->where('animal_audition_id', $audition->id)->delete();
            $photos = $this->storeUploadedPhotos($request, 'photos', 'animal-audition');
            foreach ($photos as $img) {
                DB::table('animal_audition_photos')->insert([
                    'animal_audition_id' => $audition->id,
                    'image' => $img,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return response()->json(['status' => "true", 'message' => "Animal audition updated successfully", 'data' => $audition]);
    }

    /**
     * Producer's own posts, home-tab list. status: all | active | inactive | expired.
     */
    public function animalAuditionList(Request $request)
    {
        $rules = ['user_id' => 'required|exists:users,id', 'device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $rows = DB::table('animal_auditions as aa')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'aa.animal_species_id')
            ->leftJoin('animal_breeds as br', 'br.id', '=', 'aa.animal_breed_id')
            ->where('aa.user_id', $request->user_id)
            ->select('aa.*', 'sp.name as species_name', 'br.name as breed_name')
            ->orderBy('aa.id', 'desc')
            ->get();

        $filterStatus = $request->status;
        $data = $rows->map(function ($row) {
            $row->effective_status = $this->effectiveAuditionStatus($row);
            $row->photos = DB::table('animal_audition_photos')->where('animal_audition_id', $row->id)->pluck('image');
            $row->applicant_count = DB::table('animal_audition_applications')->where('animal_audition_id', $row->id)->count();
            return $row;
        });

        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $data = $data->filter(function ($row) use ($filterStatus) {
                return $row->effective_status === $filterStatus;
            })->values();
        }

        return response()->json(['status' => "true", 'message' => "Animal audition list", 'data' => $data]);
    }

    /**
     * Full post detail. Pass viewer_user_id to also get that user's own
     * application (if any) inline — used by the auditioner detail screen
     * to show "you applied with: <profiles>" / shortlisted state without
     * a second call.
     */
    public function animalAuditionDetail(Request $request)
    {
        $rules = ['id' => 'required|exists:animal_auditions,id', 'device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $row = DB::table('animal_auditions as aa')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'aa.animal_species_id')
            ->leftJoin('animal_breeds as br', 'br.id', '=', 'aa.animal_breed_id')
            ->leftJoin('users as u', 'u.id', '=', 'aa.user_id')
            ->where('aa.id', $request->id)
            ->select('aa.*', 'sp.name as species_name', 'br.name as breed_name', 'u.name as producer_name', 'u.image as producer_image')
            ->first();

        if (!$row) {
            return response()->json(['status' => "false", 'message' => "Animal audition post not found"]);
        }

        $row->effective_status = $this->effectiveAuditionStatus($row);
        $row->photos = DB::table('animal_audition_photos')->where('animal_audition_id', $row->id)->pluck('image');
        $row->applicant_count = DB::table('animal_audition_applications')->where('animal_audition_id', $row->id)->count();

        if (!empty($request->viewer_user_id)) {
            $application = DB::table('animal_audition_applications')
                ->where('animal_audition_id', $row->id)
                ->where('user_id', $request->viewer_user_id)
                ->first();
            if ($application) {
                $application->profiles = DB::table('animal_audition_application_profiles as aap')
                    ->join('animal_profiles as ap', 'ap.id', '=', 'aap.animal_profile_id')
                    ->where('aap.animal_audition_application_id', $application->id)
                    ->select('ap.*')
                    ->get();
                foreach ($application->profiles as $p) {
                    $p->photos = DB::table('animal_profile_photos')->where('animal_profile_id', $p->id)->pluck('image');
                }
            }
            $row->my_application = $application;
        }

        return response()->json(['status' => "true", 'message' => "Animal audition detail", 'data' => $row]);
    }

    /**
     * Auditioner browse feed: active, non-expired posts only.
     */
    public function animalAuditionFeed(Request $request)
    {
        $rules = ['device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $rows = DB::table('animal_auditions as aa')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'aa.animal_species_id')
            ->leftJoin('animal_breeds as br', 'br.id', '=', 'aa.animal_breed_id')
            ->where('aa.status', 1)
            ->where(function ($q) {
                $q->whereNull('aa.expire_date')->orWhere('aa.expire_date', '>=', Carbon::now());
            })
            ->select('aa.*', 'sp.name as species_name', 'br.name as breed_name')
            ->orderBy('aa.id', 'desc')
            ->get();

        foreach ($rows as $row) {
            $row->photos = DB::table('animal_audition_photos')->where('animal_audition_id', $row->id)->pluck('image');
        }

        return response()->json(['status' => "true", 'message' => "Animal audition feed", 'data' => $rows]);
    }

    /* ------------------------------------------------------------------ *
     *  Auditioner: apply / applications
     * ------------------------------------------------------------------ */

    public function animalAuditionApply(Request $request)
    {
        $rules = [
            'animal_audition_id' => 'required|exists:animal_auditions,id',
            'user_id' => 'required|exists:users,id',
            'animal_profile_ids' => 'required|array|min:1',
            'animal_profile_ids.*' => 'exists:animal_profiles,id',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $audition = DB::table('animal_auditions')->where('id', $request->animal_audition_id)->first();
        if (!$audition || (string) $audition->status !== '1') {
            return response()->json(['status' => "false", 'message' => "This animal audition post is not currently active"]);
        }

        // Only profiles owned by this auditioner may be attached.
        $ownedCount = DB::table('animal_profiles')
            ->where('user_id', $request->user_id)
            ->whereIn('id', $request->animal_profile_ids)
            ->count();
        if ($ownedCount !== count($request->animal_profile_ids)) {
            return response()->json(['status' => "false", 'message' => "One or more selected animal profiles do not belong to you"]);
        }

        $application = AnimalAuditionApplication::create([
            'animal_audition_id' => $request->animal_audition_id,
            'user_id' => $request->user_id,
            'is_selected' => '0',
        ]);

        foreach (array_unique($request->animal_profile_ids) as $profileId) {
            DB::table('animal_audition_application_profiles')->insert([
                'animal_audition_application_id' => $application->id,
                'animal_profile_id' => $profileId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $producer = DB::table('users')->where('id', $audition->user_id)->first();
        $applicant = DB::table('users')->where('id', $request->user_id)->first();
        $applicantName = $applicant->name ?? 'An auditioner';
        $message = "{$applicantName} applied to your animal audition \"{$audition->title}\" with an animal profile";

        if ($producer && $producer->device_token) {
            $this->sendUserNotification([
                'fcmToken' => $producer->device_token,
                'title' => $audition->title,
                'message' => $message,
                'customData' => ['type' => 'animal_audition_applied', 'id' => (string) $audition->id],
            ]);
        }
        if ($producer) {
            $this->saveNotification([
                'user_id' => $producer->id,
                'title' => $audition->title,
                'message' => $message,
                'type' => 'animal_audition_applied',
                'ref_id' => $application->id,
                'custom_data' => json_encode(['type' => 'animal_audition_applied', 'id' => $audition->id]),
            ]);
        }

        return response()->json(['status' => "true", 'message' => "Applied successfully", 'data' => $application]);
    }

    /**
     * Auditioner's own applications. status: all | applied | shortlisted.
     */
    public function animalAuditionAppliedList(Request $request)
    {
        $rules = ['user_id' => 'required|exists:users,id', 'device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $rows = DB::table('animal_audition_applications as app')
            ->join('animal_auditions as aa', 'aa.id', '=', 'app.animal_audition_id')
            ->where('app.user_id', $request->user_id)
            ->select('app.*', 'aa.title', 'aa.location', 'aa.audition_date', 'aa.status as post_status', 'aa.expire_date')
            ->orderBy('app.id', 'desc')
            ->get();

        $filterStatus = $request->status;
        foreach ($rows as $row) {
            $row->profiles = DB::table('animal_audition_application_profiles as aap')
                ->join('animal_profiles as ap', 'ap.id', '=', 'aap.animal_profile_id')
                ->where('aap.animal_audition_application_id', $row->id)
                ->select('ap.*')
                ->get();
        }

        if ($filterStatus === 'shortlisted') {
            $rows = $rows->filter(function ($r) {
                return (string) $r->is_selected === '1';
            })->values();
        } elseif ($filterStatus === 'applied') {
            $rows = $rows->filter(function ($r) {
                return (string) $r->is_selected !== '1';
            })->values();
        }

        return response()->json(['status' => "true", 'message' => "Your animal audition applications", 'data' => $rows]);
    }

    /* ------------------------------------------------------------------ *
     *  Producer: applicants / shortlist
     * ------------------------------------------------------------------ */

    public function animalAuditionApplicants(Request $request)
    {
        $rules = [
            'animal_audition_id' => 'required|exists:animal_auditions,id',
            'user_id' => 'required|exists:users,id',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $audition = DB::table('animal_auditions')->where('id', $request->animal_audition_id)->first();
        if (!$audition || (string) $audition->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "You do not own this post"]);
        }

        $rows = DB::table('animal_audition_applications as app')
            ->join('users as u', 'u.id', '=', 'app.user_id')
            ->where('app.animal_audition_id', $request->animal_audition_id)
            ->select('app.*', 'u.name as applicant_name', 'u.image as applicant_image', 'u.email as applicant_email')
            ->orderBy('app.id', 'desc')
            ->get();

        foreach ($rows as $row) {
            $row->profiles = DB::table('animal_audition_application_profiles as aap')
                ->join('animal_profiles as ap', 'ap.id', '=', 'aap.animal_profile_id')
                ->leftJoin('animal_species as sp', 'sp.id', '=', 'ap.animal_species_id')
                ->leftJoin('animal_breeds as br', 'br.id', '=', 'ap.animal_breed_id')
                ->where('aap.animal_audition_application_id', $row->id)
                ->select('ap.*', 'sp.name as species_name', 'br.name as breed_name')
                ->get();
            foreach ($row->profiles as $p) {
                $p->photos = DB::table('animal_profile_photos')->where('animal_profile_id', $p->id)->pluck('image');
            }
        }

        return response()->json(['status' => "true", 'message' => "Applicants fetched successfully", 'data' => $rows]);
    }

    public function selectAnimalAuditionApplicant(Request $request)
    {
        return $this->setApplicantSelection($request, '1');
    }

    public function deselectAnimalAuditionApplicant(Request $request)
    {
        return $this->setApplicantSelection($request, '0');
    }

    private function setApplicantSelection(Request $request, $isSelected)
    {
        $rules = ['id' => 'required|exists:animal_audition_applications,id', 'device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $updated = DB::table('animal_audition_applications')->where('id', $request->id)->update(['is_selected' => $isSelected]);
        if (!$updated) {
            return response()->json(['status' => "false", 'message' => "Failed to update the applicant"]);
        }

        if ($isSelected === '1') {
            $row = DB::table('animal_audition_applications as app')
                ->join('users as u', 'u.id', '=', 'app.user_id')
                ->join('animal_auditions as aa', 'aa.id', '=', 'app.animal_audition_id')
                ->where('app.id', $request->id)
                ->select('u.*', 'aa.title as post_title', 'aa.id as animal_audition_id')
                ->first();

            if ($row && $row->device_token) {
                $this->sendUserNotification([
                    'fcmToken' => $row->device_token,
                    'title' => $row->post_title,
                    'message' => "Congratulations! Your animal profile was shortlisted for \"{$row->post_title}\"",
                    'customData' => ['type' => 'animal_audition_selected', 'id' => (string) $row->animal_audition_id],
                ]);
            }
            if ($row) {
                $this->saveNotification([
                    'user_id' => $row->id,
                    'title' => $row->post_title,
                    'message' => "Congratulations! Your animal profile was shortlisted for \"{$row->post_title}\"",
                    'type' => 'animal_audition_selected',
                    'ref_id' => $request->id,
                    'custom_data' => json_encode(['type' => 'animal_audition_selected', 'id' => $row->animal_audition_id]),
                ]);
            }
        }

        return response()->json(['status' => "true", 'message' => $isSelected === '1' ? "Applicant shortlisted successfully" : "Applicant de-selected successfully"]);
    }

    /* ------------------------------------------------------------------ *
     *  Auditioner: animal profiles (reusable, own management screen)
     * ------------------------------------------------------------------ */

    public function addAnimalProfile(Request $request)
    {
        $rules = array_merge([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'animal_species_id' => 'required|exists:animal_species,id',
            'device_type' => 'required|string',
        ], $this->animalFieldRules());
        $rules['animal_species_id'] = 'required|exists:animal_species,id';

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $profile = AnimalProfile::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'animal_species_id' => $request->animal_species_id,
            'animal_breed_id' => $request->animal_breed_id ?: null,
            'age_range' => $request->age_range,
            'gender' => $request->gender,
            'size' => $request->size,
            'weight' => $request->weight ?: null,
            'color_markings' => $request->color_markings,
            'temperament' => $request->temperament,
            'status' => 1,
        ]);

        $photos = $this->storeUploadedPhotos($request, 'photos', 'animal-profile');
        foreach ($photos as $img) {
            DB::table('animal_profile_photos')->insert([
                'animal_profile_id' => $profile->id,
                'image' => $img,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['status' => "true", 'message' => "Animal profile created successfully", 'data' => $profile]);
    }

    public function updateAnimalProfile(Request $request)
    {
        $rules = [
            'id' => 'required|exists:animal_profiles,id',
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $profile = AnimalProfile::find($request->id);
        if ((string) $profile->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "You do not own this animal profile"]);
        }

        $profile->name = $request->name;
        $profile->animal_species_id = $request->animal_species_id ?: $profile->animal_species_id;
        $profile->animal_breed_id = $request->animal_breed_id ?: $profile->animal_breed_id;
        $profile->age_range = $request->age_range ?? $profile->age_range;
        $profile->gender = $request->gender ?? $profile->gender;
        $profile->size = $request->size ?? $profile->size;
        $profile->weight = $request->weight ?? $profile->weight;
        $profile->color_markings = $request->color_markings ?? $profile->color_markings;
        $profile->temperament = $request->temperament ?? $profile->temperament;
        $profile->save();

        if ($request->hasFile('photos')) {
            DB::table('animal_profile_photos')->where('animal_profile_id', $profile->id)->delete();
            $photos = $this->storeUploadedPhotos($request, 'photos', 'animal-profile');
            foreach ($photos as $img) {
                DB::table('animal_profile_photos')->insert([
                    'animal_profile_id' => $profile->id,
                    'image' => $img,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return response()->json(['status' => "true", 'message' => "Animal profile updated successfully", 'data' => $profile]);
    }

    public function deleteAnimalProfile(Request $request)
    {
        $rules = ['id' => 'required|exists:animal_profiles,id', 'user_id' => 'required|exists:users,id'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $profile = DB::table('animal_profiles')->where('id', $request->id)->first();
        if (!$profile || (string) $profile->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "You do not own this animal profile"]);
        }

        DB::table('animal_profile_photos')->where('animal_profile_id', $request->id)->delete();
        DB::table('animal_profiles')->where('id', $request->id)->delete();

        return response()->json(['status' => "true", 'message' => "Animal profile deleted successfully"]);
    }

    public function animalProfileList(Request $request)
    {
        $rules = ['user_id' => 'required|exists:users,id', 'device_type' => 'required|string'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => "false", 'message' => $this->validationHandle($validator->messages())]);
        }

        $rows = DB::table('animal_profiles as ap')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'ap.animal_species_id')
            ->leftJoin('animal_breeds as br', 'br.id', '=', 'ap.animal_breed_id')
            ->where('ap.user_id', $request->user_id)
            ->select('ap.*', 'sp.name as species_name', 'br.name as breed_name')
            ->orderBy('ap.id', 'desc')
            ->get();

        foreach ($rows as $row) {
            $row->photos = DB::table('animal_profile_photos')->where('animal_profile_id', $row->id)->pluck('image');
        }

        return response()->json(['status' => "true", 'message' => "Your animal profiles", 'data' => $rows]);
    }

    /**
     * Unauthenticated cron sweep, mirrors expire-audition-invites /
     * checkexpireaudition. Purely for reporting/cleanliness — every read
     * endpoint above already computes expiry live via effectiveAuditionStatus().
     */
    public function expireAnimalAuditions()
    {
        $count = DB::table('animal_auditions')
            ->where('status', 1)
            ->whereNotNull('expire_date')
            ->where('expire_date', '<', Carbon::now())
            ->update(['status' => 0]);

        return response()->json(['status' => "true", 'message' => "Expired {$count} animal audition post(s)"]);
    }
}
