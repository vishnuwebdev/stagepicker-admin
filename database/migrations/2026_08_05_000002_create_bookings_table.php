<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enrollment/registration record for classes (and, later, webinars/seminars
 * — see `bookable_type`, only 'class' is wired up in this pass). Plays the
 * same role `orders` plays for merchandise: created as `pending_payment`,
 * then confirmed/cancelled by api/PaymentController@cascadeToBooking once
 * the linked Stripe PaymentIntent resolves (payable_type=booking,
 * payable_id=this row's id) — see WEBINAR_SEMINAR_BOOKING_PLAN.md.
 *
 * title_snapshot/price_snapshot follow the same pattern as order_items:
 * don't trust a live join back to a catalog row that might be repriced or
 * renamed later.
 */
class CreateBookingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');

                // class | webinar | seminar — only 'class' is created by the
                // app today.
                $table->string('bookable_type');
                $table->unsignedBigInteger('bookable_id');

                $table->string('title_snapshot');
                $table->decimal('price_snapshot', 10, 2);
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('amount', 10, 2);

                // online | offline — for a class with mode=both this is
                // whichever the user picked at checkout; for mode=online/
                // offline it's just copied from the item, since there was
                // nothing to choose. Determines whether get_booking_detail
                // returns `link`, `location`, or both.
                $table->string('attended_mode')->nullable();

                // pending_payment -> confirmed | cancelled. No 'refunded'
                // status here — same as orders, refunds are handled
                // manually by admin in the Stripe dashboard, not modeled
                // in-app.
                $table->string('status')->default('pending_payment');

                $table->timestamp('booked_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();

                $table->timestamps();

                $table->index('user_id');
                $table->index(['bookable_type', 'bookable_id']);
                $table->index('status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
