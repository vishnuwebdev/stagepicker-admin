<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Invite for Audition" feature — a producer picks shortlisted auditioners
 * from a post's participant list and sends them a callback invite with its
 * own title/location/date/time. Two tables:
 *
 *  - audition_invites: one row per invite the producer sends (the "event").
 *    A single post can have many of these over time (e.g. a first round,
 *    then a callback round) — each is independent.
 *  - audition_invite_participants: one row per auditioner invited to a
 *    given audition_invites row, holding their individual response.
 *
 * post_audition_id / producer_id / user_id are plain indexed columns, not
 * foreign keys — post_auditions and users predate this app's migrations
 * (see CLAUDE.md: "migrations do not reflect the real schema"), so there's
 * no Laravel-tracked schema to bind a constraint to safely. This matches
 * how every other table in this app references post_auditions/users today.
 *
 * Expiry is intentionally NOT a stored boolean/status transition driven
 * only by cron: AuditionInviteController computes the effective status
 * (falling pending invites to "expired" once audition_date/end_time has
 * passed) on every read, so the API is correct immediately rather than
 * only after the next cron tick. The `status` column here still gets
 * flipped to 'expired' by a cron endpoint (mirroring the existing
 * checkexpireaudition/notifybeforeexpire pattern) purely for
 * reporting/cleanliness — the app must not rely on it being fresh.
 */
class CreateAuditionInvitesTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('audition_invites')) {
            Schema::create('audition_invites', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_audition_id');
                $table->unsignedBigInteger('producer_id');

                $table->string('title');
                $table->string('location');
                $table->date('audition_date');
                $table->time('start_time');
                $table->time('end_time')->nullable();

                $table->timestamps();

                $table->index('post_audition_id');
                $table->index('producer_id');
            });
        }

        if (!Schema::hasTable('audition_invite_participants')) {
            Schema::create('audition_invite_participants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('audition_invite_id');
                $table->unsignedBigInteger('user_id');

                // Traceability back to the shortlist row this invite came
                // from. Nullable — not required for the invite to function.
                $table->unsignedBigInteger('post_audition_participant_id')->nullable();

                // pending | accepted | declined | expired
                $table->string('status')->default('pending');
                $table->timestamp('responded_at')->nullable();

                $table->timestamps();

                $table->index('audition_invite_id');
                $table->index('user_id');
                $table->index('status');
                // One invite row per person per invite batch.
                $table->unique(['audition_invite_id', 'user_id'], 'aip_invite_user_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('audition_invite_participants');
        Schema::dropIfExists('audition_invites');
    }
}
