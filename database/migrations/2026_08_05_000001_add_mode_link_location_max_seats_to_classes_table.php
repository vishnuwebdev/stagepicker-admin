<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds post-purchase access fields to `classes` — see
 * WEBINAR_SEMINAR_BOOKING_PLAN.md "Post-purchase access". A class can be
 * `online` (join link only), `offline` (address only), or `both` — in the
 * `both` case the user picks which one they're attending on
 * class_detail.dart's existing "How would you like to join?" toggle, and
 * that choice is recorded per-booking (see bookings.attended_mode), not
 * here. `link`/`location` are never returned by the public catalog
 * endpoints (get_data, get_class_details) — only by get_booking_detail,
 * and only once that booking is confirmed. See api/BookingController.php.
 *
 * `max_seats` is nullable = unlimited, matching how merchandise stock isn't
 * tracked either — existing rows are unaffected.
 */
class AddModeLinkLocationMaxSeatsToClassesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('classes') && !Schema::hasColumn('classes', 'mode')) {
            Schema::table('classes', function (Blueprint $table) {
                // online | offline | both
                $table->string('mode')->default('online')->after('price');
                $table->string('link')->nullable()->after('mode');
                $table->string('location')->nullable()->after('link');
                $table->unsignedInteger('max_seats')->nullable()->after('location');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'mode')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn(['mode', 'link', 'location', 'max_seats']);
            });
        }
    }
}
