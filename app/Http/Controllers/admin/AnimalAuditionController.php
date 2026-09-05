<?php
namespace App\Http\Controllers\Admin;

use App\Models\AnimalAudition;
use App\Services\FCMService;
use App\Http\Controllers\AdminController;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Admin visibility into the Animal Audition feature: view every post
 * across all producers, drill into one post's full detail + applicants +
 * their submitted animal profiles, and activate/deactivate a post.
 *
 * Deliberately view + moderate only — no create/edit/delete of posts on a
 * producer's behalf, per the "admin gets view/moderate/master-data
 * control, not full CRUD" decision for this feature. Species/breed master
 * data lives in AnimalSpeciesController / AnimalBreedController.
 *
 * Permission module: p10 (Animal Audition Management) — same module as
 * the species/breed master-data controllers, so one permission grant
 * covers the whole feature's admin surface.
 *
 * Uses FCMService (the modern path already used by the AnimalAudition API
 * controller) for the status-change notification, rather than
 * admin/PostauditionController::updateStatus's legacy hardcoded FCM
 * server-key + raw curl approach — that pattern is flagged as dead/legacy
 * in CLAUDE.md and shouldn't be extended into new code.
 */
class AnimalAuditionController extends AdminController
{
    public function list(Request $request)
    {
        if (checkAdminUserPermission('p10', 'view') == false) {
            return redirect("/");
        }

        $editPermission = checkAdminUserPermission('p10', 'edit');

        $animalAudition = DB::table('animal_auditions as aa')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'aa.animal_species_id')
            ->leftJoin('users', 'users.id', '=', 'aa.user_id')
            ->select('aa.*', 'sp.name as species_name', 'users.name as producer_name')
            ->orderBy('aa.id', 'DESC')
            ->get();

        return \View::make("admin/animal-audition/list", compact('animalAudition', 'editPermission'));
    }

    public function detail(Request $request, $id = null)
    {
        if (checkAdminUserPermission('p10', 'view') == false) {
            return redirect("/");
        }

        $id = base64_decode($id);

        $detail = DB::table('animal_auditions as aa')
            ->leftJoin('animal_species as sp', 'sp.id', '=', 'aa.animal_species_id')
            ->leftJoin('animal_breeds as br', 'br.id', '=', 'aa.animal_breed_id')
            ->leftJoin('users', 'users.id', '=', 'aa.user_id')
            ->select('aa.*', 'sp.name as species_name', 'br.name as breed_name', 'users.name as producer_name', 'users.email as producer_email')
            ->where('aa.id', $id)
            ->first();

        $photos = collect();
        $applicants = collect();

        if (!empty($detail)) {
            $photos = DB::table('animal_audition_photos')->where('animal_audition_id', $id)->get();

            $applicants = DB::table('animal_audition_applications as app')
                ->join('users', 'users.id', '=', 'app.user_id')
                ->select('app.*', 'users.name as applicant_name', 'users.email as applicant_email')
                ->where('app.animal_audition_id', $id)
                ->orderBy('app.id', 'DESC')
                ->get();

            foreach ($applicants as $applicant) {
                $applicant->profiles = DB::table('animal_audition_application_profiles as aap')
                    ->join('animal_profiles as ap', 'ap.id', '=', 'aap.animal_profile_id')
                    ->leftJoin('animal_species as sp', 'sp.id', '=', 'ap.animal_species_id')
                    ->leftJoin('animal_breeds as br', 'br.id', '=', 'ap.animal_breed_id')
                    ->select('ap.*', 'sp.name as species_name', 'br.name as breed_name')
                    ->where('aap.animal_audition_application_id', $applicant->id)
                    ->get();
                foreach ($applicant->profiles as $profile) {
                    $profile->photos = DB::table('animal_profile_photos')->where('animal_profile_id', $profile->id)->get();
                }
            }
        }

        return \View::make('admin/animal-audition/detail', compact('detail', 'photos', 'applicants'));
    }

    public function updateStatus(Request $request)
    {
        if (checkAdminUserPermission('p10', 'edit') == false) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to do this.']);
        }

        $data = $request->all();
        $rules = ['Id' => 'required|exists:animal_auditions,id'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $audition = AnimalAudition::find($data['Id']);
        $audition->status = $data['status'];
        $audition->save();

        $producer = DB::table('users')->where('id', $audition->user_id)->first();
        if ($producer) {
            $message = $data['status'] == '1'
                ? "Administrator activated your animal audition post \"{$audition->title}\"."
                : "Administrator deactivated your animal audition post \"{$audition->title}\".";

            if (!empty($producer->device_token)) {
                try {
                    $fcmService = new FCMService();
                    $fcmService->sendNotification(
                        $producer->device_token,
                        $data['status'] == '1' ? 'Post activated' : 'Post deactivated',
                        $message,
                        ['type' => 'animal_audition_status_changed', 'id' => (string) $audition->id]
                    );
                } catch (\Throwable $e) {
                    // Never let a push failure block the admin action.
                }
            }

            DB::table('notifications')->insert([
                'user_id' => $producer->id,
                'title' => $data['status'] == '1' ? 'Post activated' : 'Post deactivated',
                'body' => $message,
                'type' => 'animal_audition_status_changed',
                'ref_id' => $audition->id,
                'is_read' => false,
                'custom_data' => json_encode(['type' => 'animal_audition_status_changed', 'id' => $audition->id]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return response()->json(['success' => true], 200);
    }
}
