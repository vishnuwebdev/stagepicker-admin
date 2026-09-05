<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Animal Audition feature — producers post a casting call describing the
 * animal they need, auditioners apply with one or more of their own
 * reusable animal profiles, producers shortlist and (via the existing
 * Firestore chat flow) start a conversation. Admin gets view/moderate
 * access plus master-data control over species/breeds.
 *
 * Self-contained module, modeled on the Audition Invites migration
 * (2026_08_19_000001_create_audition_invites_tables.php): no foreign key
 * constraints — post_auditions/users predate this app's migrations (see
 * CLAUDE.md: "migrations do not reflect the real schema") and every other
 * table in this app that references them does so with a plain indexed
 * unsignedBigInteger column instead of a real FK, so this follows suit.
 *
 * Tables:
 *  - animal_species / animal_breeds: admin-managed master lists (breeds
 *    cascade from a species) so the mobile dropdowns stay consistent.
 *  - animal_profiles (+ animal_profile_photos): an auditioner's own
 *    reusable animal record — created once, applied with many times.
 *  - animal_auditions (+ animal_audition_photos): a producer's casting
 *    post describing the animal they're casting for.
 *  - animal_audition_applications: one row per apply action (mirrors
 *    post_audition_participants' is_selected shortlist flag).
 *  - animal_audition_application_profiles: join table, since one
 *    application can bundle more than one of the auditioner's animal
 *    profiles.
 *
 * Expiry follows the same pattern as Audition Invites: `status` on
 * animal_auditions is flipped by a cron sweep purely for
 * reporting/cleanliness, but list/detail endpoints must compute the
 * effective "expired" state live from audition_date on every read rather
 * than trust the cron has run recently.
 */
class CreateAnimalAuditionsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('animal_species')) {
            Schema::create('animal_species', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                // 1 = active, 0 = inactive/hidden from dropdowns.
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('animal_breeds')) {
            Schema::create('animal_breeds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('animal_species_id');
                $table->string('name');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->index('animal_species_id');
            });
        }

        if (!Schema::hasTable('animal_profiles')) {
            Schema::create('animal_profiles', function (Blueprint $table) {
                $table->id();
                // Owning auditioner.
                $table->unsignedBigInteger('user_id');

                $table->string('name'); // the pet's own name
                $table->unsignedBigInteger('animal_species_id');
                $table->unsignedBigInteger('animal_breed_id')->nullable();

                // puppy_kitten | juvenile | adult | senior
                $table->string('age_range')->nullable();
                // male | female | unknown
                $table->string('gender')->nullable();
                // small | medium | large
                $table->string('size')->nullable();
                $table->decimal('weight', 8, 2)->nullable();
                $table->text('color_markings')->nullable();
                $table->text('temperament')->nullable();

                // 1 = active/visible for applying, 0 = archived by the auditioner.
                $table->tinyInteger('status')->default(1);

                $table->timestamps();

                $table->index('user_id');
                $table->index('animal_species_id');
                $table->index('animal_breed_id');
            });
        }

        if (!Schema::hasTable('animal_profile_photos')) {
            Schema::create('animal_profile_photos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('animal_profile_id');
                $table->string('image');
                $table->timestamps();

                $table->index('animal_profile_id');
            });
        }

        if (!Schema::hasTable('animal_auditions')) {
            Schema::create('animal_auditions', function (Blueprint $table) {
                $table->id();
                // Owning producer.
                $table->unsignedBigInteger('user_id');

                $table->string('title');
                $table->text('description')->nullable();
                $table->string('location')->nullable();
                $table->date('audition_date')->nullable();
                $table->time('audition_time')->nullable();
                // audition_date + audition_time combined, mirrors
                // Postaudition::expire_date — used for live expiry checks.
                $table->dateTime('expire_date')->nullable();

                $table->tinyInteger('compensation')->default(0);
                $table->text('compensation_description')->nullable();
                $table->string('production_website')->nullable();

                // Desired-animal fields — all nullable, empty = "any".
                $table->unsignedBigInteger('animal_species_id')->nullable();
                $table->unsignedBigInteger('animal_breed_id')->nullable();
                $table->string('age_range')->nullable();
                $table->string('gender')->nullable();
                $table->string('size')->nullable();
                $table->text('color_markings')->nullable();
                $table->text('temperament')->nullable();

                // 1 = active, 0 = inactive (producer-controlled). Expiry
                // is computed live from expire_date, not solely from this.
                $table->tinyInteger('status')->default(1);

                $table->timestamps();

                $table->index('user_id');
                $table->index('animal_species_id');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('animal_audition_photos')) {
            Schema::create('animal_audition_photos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('animal_audition_id');
                $table->string('image');
                $table->timestamps();

                $table->index('animal_audition_id');
            });
        }

        if (!Schema::hasTable('animal_audition_applications')) {
            Schema::create('animal_audition_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('animal_audition_id');
                // Applying auditioner.
                $table->unsignedBigInteger('user_id');

                // '0' | '1' — string to match post_audition_participants.is_selected.
                $table->string('is_selected')->default('0');

                $table->timestamps();

                $table->index('animal_audition_id');
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('animal_audition_application_profiles')) {
            Schema::create('animal_audition_application_profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('animal_audition_application_id');
                $table->unsignedBigInteger('animal_profile_id');
                $table->timestamps();

                // MySQL's default auto-generated index name for this
                // table+column combo (75 chars) exceeds the 64-char
                // identifier limit, so these are explicitly named short.
                $table->index('animal_audition_application_id', 'aaap_application_id_idx');
                $table->index('animal_profile_id', 'aaap_profile_id_idx');
                $table->unique(
                    ['animal_audition_application_id', 'animal_profile_id'],
                    'aaap_application_profile_unique'
                );
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('animal_audition_application_profiles');
        Schema::dropIfExists('animal_audition_applications');
        Schema::dropIfExists('animal_audition_photos');
        Schema::dropIfExists('animal_auditions');
        Schema::dropIfExists('animal_profile_photos');
        Schema::dropIfExists('animal_profiles');
        Schema::dropIfExists('animal_breeds');
        Schema::dropIfExists('animal_species');
    }
}
