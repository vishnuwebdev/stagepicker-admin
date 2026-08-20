<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditionInvite;
use App\Models\AuditionInviteParticipant;
use App\Services\FCMService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * "Invite for Audition" feature.
 *
 * Deliberately NOT extending App\Http\Controllers\ApiController: that base
 * class's __destruct() calls a jsonView() method that is commented out in
 * the class body, which throws on every request that reaches it (it's
 * silent today only because it fires after the response has already been
 * sent). PostauditionController inherits that latent bug; this controller
 * avoids it by extending the plain Controller base instead, matching
 * AuditionExpireCheckController. validationHandle/sendUserNotification/
 * saveNotification are re-declared locally below, mirroring how
 * PostauditionController defines its own copies (they aren't shared via a
 * trait in this codebase today).
 *
 * Same trust model as the rest of api/*: no auth middleware, endpoints
 * trust the client-supplied user_id. Not introducing real auth here per
 * CLAUDE.md guidance — flagged as existing, not new, risk.
 */
class AuditionInviteController extends Controller
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
            return;
        }
        try {
            $fcmService = new FCMService();
            $fcmService->sendNotification(
                $data['fcmToken'],
                $data['title'],
                $data['message'],
                $data['customData'] ?? []
            );
        } catch (\Exception $e) {
            // Never let a push failure break the API response — mirrors
            // the forgiving pattern used elsewhere for FCM sends.
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
     * now() > audition_date + (end_time ?? start_time) => expired.
     */
    private function isPastAuditionWindow($auditionDate, $startTime, $endTime)
    {
        $cutoff = $auditionDate . ' ' . ($endTime ?: $startTime);
        try {
            return Carbon::parse($cutoff)->isPast();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Effective status for display: flips a still-"pending" row to
     * "expired" once the audition window has passed, without needing the
     * expireAuditionInvites cron to have run yet. Opportunistically
     * persists the flip so the DB stays close to correct even if the app
     * never re-queries this exact row again.
     */
    private function effectiveStatus($participantRow, $auditionDate, $startTime, $endTime)
    {
        if ($participantRow->status === AuditionInviteParticipant::STATUS_PENDING
            && $this->isPastAuditionWindow($auditionDate, $startTime, $endTime)) {
            DB::table('audition_invite_participants')
                ->where('id', $participantRow->id)
                ->where('status', AuditionInviteParticipant::STATUS_PENDING)
                ->update(['status' => AuditionInviteParticipant::STATUS_EXPIRED]);
            return AuditionInviteParticipant::STATUS_EXPIRED;
        }
        return $participantRow->status;
    }

    /**
     * Producer sends an audition invite to one or more shortlisted
     * auditioners from a post's participant list.
     */
    public function sendAuditionInvite(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'post_audition_id' => 'required|exists:post_auditions,id',
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'audition_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'participant_user_ids' => 'required|array|min:1',
            'device_type' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "false",
                'message' => $this->validationHandle($validator->messages()),
            ]);
        }

        $post = DB::table('post_auditions')->where('id', $request->post_audition_id)->first();
        if (!$post) {
            return response()->json(['status' => "false", 'message' => "Post not found"]);
        }
        if ((string) $post->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "You do not own this post"]);
        }

        // Only people actually shortlisted (is_selected=1) for this post are
        // eligible — matches the existing selectParticipant/deSelectParticipant
        // flow this feature is invoked from.
        $eligibleParticipants = DB::table('post_audition_participants')
            ->where('post_audition_id', $request->post_audition_id)
            ->where('is_selected', '1')
            ->whereIn('user_id', $request->participant_user_ids)
            ->get()
            ->keyBy('user_id');

        if ($eligibleParticipants->isEmpty()) {
            return response()->json([
                'status' => "false",
                'message' => "None of the selected auditioners are shortlisted for this post",
            ]);
        }

        $invite = AuditionInvite::create([
            'post_audition_id' => $request->post_audition_id,
            'producer_id' => $request->user_id,
            'title' => $request->title,
            'location' => $request->location,
            'audition_date' => $request->audition_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        $invitedCount = 0;
        $skipped = count($request->participant_user_ids) - $eligibleParticipants->count();

        foreach ($eligibleParticipants as $userId => $participantRow) {
            AuditionInviteParticipant::create([
                'audition_invite_id' => $invite->id,
                'user_id' => $userId,
                'post_audition_participant_id' => $participantRow->id,
                'status' => AuditionInviteParticipant::STATUS_PENDING,
            ]);
            $invitedCount++;

            $userData = DB::table('users')->where('id', $userId)->first();
            if ($userData && $userData->device_token) {
                $this->sendUserNotification([
                    'fcmToken' => $userData->device_token,
                    'title' => "You're invited to an audition!",
                    'message' => "You've been invited for \"{$request->title}\" — {$post->audition_title}",
                    'customData' => [
                        'type' => 'audition_invite_received',
                        'id' => (string) $invite->id,
                    ],
                ]);
            }
            $this->saveNotification([
                'user_id' => $userId,
                'title' => "You're invited to an audition!",
                'message' => "You've been invited for \"{$request->title}\" — {$post->audition_title}",
                'type' => 'audition_invite_received',
                'ref_id' => $invite->id,
                'custom_data' => json_encode([
                    'type' => 'audition_invite_received',
                    'id' => $invite->id,
                ]),
            ]);
        }

        return response()->json([
            'status' => "true",
            'message' => $skipped > 0
                ? "Invite sent to {$invitedCount} auditioner(s). {$skipped} of the selected people were skipped (not shortlisted for this post)."
                : "Invite sent to {$invitedCount} auditioner(s).",
            'data' => [
                'id' => $invite->id,
                'invited_count' => $invitedCount,
            ],
        ]);
    }

    /**
     * Auditioner's flat list of every invite sent to them. The client is
     * expected to split this into "Active" (pending/accepted) and
     * "Declined & Expired" tabs itself, same as how the rest of this API
     * returns full lists for the client to group/filter.
     */
    public function myAuditionInvites(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "false",
                'message' => $this->validationHandle($validator->messages()),
            ]);
        }

        $rows = DB::table('audition_invite_participants as aip')
            ->join('audition_invites as ai', 'ai.id', '=', 'aip.audition_invite_id')
            ->join('post_auditions as pa', 'pa.id', '=', 'ai.post_audition_id')
            ->leftJoin('users as producer', 'producer.id', '=', 'ai.producer_id')
            ->where('aip.user_id', $request->user_id)
            ->select(
                'aip.id',
                'aip.status',
                'aip.responded_at',
                'ai.id as invite_id',
                'ai.title',
                'ai.location',
                'ai.audition_date',
                'ai.start_time',
                'ai.end_time',
                'pa.id as post_audition_id',
                'pa.audition_title as post_title',
                'producer.name as producer_name'
            )
            ->orderBy('ai.audition_date', 'desc')
            ->get();

        $data = $rows->map(function ($row) {
            $row->status = $this->effectiveStatus($row, $row->audition_date, $row->start_time, $row->end_time);
            return $row;
        });

        return response()->json([
            'status' => "true",
            'message' => "Audition invites fetched successfully",
            'data' => $data,
        ]);
    }

    /**
     * Auditioner accepts or declines a pending, non-expired invite.
     */
    public function respondAuditionInvite(Request $request)
    {
        $rules = [
            'id' => 'required|exists:audition_invite_participants,id',
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:accept,decline',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "false",
                'message' => $this->validationHandle($validator->messages()),
            ]);
        }

        $participant = DB::table('audition_invite_participants')->where('id', $request->id)->first();
        if (!$participant || (string) $participant->user_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "Invite not found"]);
        }

        $invite = DB::table('audition_invites')->where('id', $participant->audition_invite_id)->first();
        $currentStatus = $this->effectiveStatus($participant, $invite->audition_date, $invite->start_time, $invite->end_time);

        if ($currentStatus !== AuditionInviteParticipant::STATUS_PENDING) {
            return response()->json([
                'status' => "false",
                'message' => $currentStatus === AuditionInviteParticipant::STATUS_EXPIRED
                    ? "This invite has expired"
                    : "You've already responded to this invite",
            ]);
        }

        $newStatus = $request->action === 'accept'
            ? AuditionInviteParticipant::STATUS_ACCEPTED
            : AuditionInviteParticipant::STATUS_DECLINED;

        DB::table('audition_invite_participants')
            ->where('id', $participant->id)
            ->update(['status' => $newStatus, 'responded_at' => Carbon::now()]);

        // Notify the producer.
        $post = DB::table('post_auditions')->where('id', $invite->post_audition_id)->first();
        $auditioner = DB::table('users')->where('id', $request->user_id)->first();
        $producer = DB::table('users')->where('id', $invite->producer_id)->first();

        $verb = $newStatus === AuditionInviteParticipant::STATUS_ACCEPTED ? 'accepted' : 'declined';
        $notifTitle = ($auditioner->name ?? 'An auditioner') . " {$verb} your invite";
        $notifBody = "\"{$invite->title}\" — " . ($post->audition_title ?? '');

        if ($producer && $producer->device_token) {
            $this->sendUserNotification([
                'fcmToken' => $producer->device_token,
                'title' => $notifTitle,
                'message' => $notifBody,
                'customData' => [
                    'type' => $newStatus === AuditionInviteParticipant::STATUS_ACCEPTED
                        ? 'audition_invite_accepted'
                        : 'audition_invite_declined',
                    'id' => (string) $invite->id,
                ],
            ]);
        }
        if ($producer) {
            $this->saveNotification([
                'user_id' => $producer->id,
                'title' => $notifTitle,
                'message' => $notifBody,
                'type' => $newStatus === AuditionInviteParticipant::STATUS_ACCEPTED
                    ? 'audition_invite_accepted'
                    : 'audition_invite_declined',
                'ref_id' => $invite->id,
                'custom_data' => json_encode([
                    'type' => $newStatus === AuditionInviteParticipant::STATUS_ACCEPTED
                        ? 'audition_invite_accepted'
                        : 'audition_invite_declined',
                    'id' => $invite->id,
                ]),
            ]);
        }

        return response()->json([
            'status' => "true",
            'message' => $newStatus === AuditionInviteParticipant::STATUS_ACCEPTED
                ? "Invite accepted"
                : "Invite declined",
        ]);
    }

    /**
     * Producer's "My Audition Invites" overview — one row per invite batch
     * they've sent (not aggregated per post, since a post can have several
     * batches), each with response counts.
     */
    public function producerAuditionInvites(Request $request)
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "false",
                'message' => $this->validationHandle($validator->messages()),
            ]);
        }

        $invites = DB::table('audition_invites as ai')
            ->join('post_auditions as pa', 'pa.id', '=', 'ai.post_audition_id')
            ->where('ai.producer_id', $request->user_id)
            ->select('ai.*', 'pa.audition_title as post_title')
            ->orderBy('ai.created_at', 'desc')
            ->get();

        $data = $invites->map(function ($invite) {
            $counts = DB::table('audition_invite_participants')
                ->where('audition_invite_id', $invite->id)
                ->selectRaw("
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted_count,
                    SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) as declined_count,
                    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_count,
                    COUNT(*) as total_count
                ")
                ->first();
            $invite->counts = $counts;
            return $invite;
        });

        return response()->json([
            'status' => "true",
            'message' => "Producer audition invites fetched successfully",
            'data' => $data,
        ]);
    }

    /**
     * Full participant breakdown for one invite batch, most recently
     * accepted auditioner surfaced separately.
     */
    public function producerAuditionInviteDetail(Request $request)
    {
        $rules = [
            'audition_invite_id' => 'required|exists:audition_invites,id',
            'user_id' => 'required|exists:users,id',
            'device_type' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "false",
                'message' => $this->validationHandle($validator->messages()),
            ]);
        }

        $invite = DB::table('audition_invites as ai')
            ->join('post_auditions as pa', 'pa.id', '=', 'ai.post_audition_id')
            ->where('ai.id', $request->audition_invite_id)
            ->select('ai.*', 'pa.audition_title as post_title')
            ->first();

        if (!$invite || (string) $invite->producer_id !== (string) $request->user_id) {
            return response()->json(['status' => "false", 'message' => "Invite not found"]);
        }

        $participants = DB::table('audition_invite_participants as aip')
            ->join('users as u', 'u.id', '=', 'aip.user_id')
            ->where('aip.audition_invite_id', $invite->id)
            ->select('aip.*', 'u.name', 'u.age', 'u.height', 'u.image')
            ->get()
            ->map(function ($row) use ($invite) {
                $row->status = $this->effectiveStatus($row, $invite->audition_date, $invite->start_time, $invite->end_time);
                return $row;
            });

        $recentlyAccepted = $participants
            ->where('status', AuditionInviteParticipant::STATUS_ACCEPTED)
            ->sortByDesc('responded_at')
            ->first();

        return response()->json([
            'status' => "true",
            'message' => "Invite detail fetched successfully",
            'data' => [
                'invite' => $invite,
                'recently_accepted' => $recentlyAccepted,
                'participants' => $participants->values(),
            ],
        ]);
    }

    /**
     * Cron/manual sweep — mirrors AuditionExpireCheckController's existing
     * unauthenticated checkexpireaudition endpoint. Flips any pending
     * invite whose audition window has passed to 'expired'. Reads
     * (myAuditionInvites, etc.) already compute this on the fly, so this
     * is a cleanliness pass, not a correctness dependency.
     */
    public function expireAuditionInvites()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $rows = DB::select("
            SELECT aip.id
            FROM audition_invite_participants aip
            JOIN audition_invites ai ON ai.id = aip.audition_invite_id
            WHERE aip.status = 'pending'
            AND CONCAT(ai.audition_date, ' ', COALESCE(ai.end_time, ai.start_time)) < ?
        ", [$now]);

        $ids = array_map(function ($row) {
            return $row->id;
        }, $rows);

        if (!empty($ids)) {
            DB::table('audition_invite_participants')
                ->whereIn('id', $ids)
                ->update(['status' => 'expired']);
        }

        return response()->json([
            'status' => "true",
            'message' => "Expired " . count($ids) . " invite(s).",
        ]);
    }
}
