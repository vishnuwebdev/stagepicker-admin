<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Booking domain for classes — plays the same role api/OrderController.php
 * plays for merchandise (create as pending_payment, then confirmed/
 * cancelled by PaymentController@cascadeToBooking once the linked Stripe
 * PaymentIntent resolves via payable_type=booking). Webinars/seminars are
 * intentionally out of scope for this pass — `bookable_type` only accepts
 * 'class' today; see WEBINAR_SEMINAR_BOOKING_PLAN.md for the fuller plan.
 *
 * Reuses api/PaymentController.php entirely unchanged for create-payment-
 * intent / sync-payment-status / the webhook / the stale-payment sweep —
 * this controller only ever creates/reads `bookings` rows.
 */
class BookingController extends ApiController
{
    /**
     * @param bool $withAccess When true and the booking is confirmed,
     *   attaches `link`/`location` per the booking's attended_mode. Never
     *   attached for a pending/cancelled booking, and never attached at all
     *   unless the caller explicitly asks — get_booking_detail asks,
     *   nothing else should.
     */
    private function serializeBooking(Booking $booking, $withAccess = false)
    {
        $data = [
            'id' => $booking->id,
            'bookable_type' => $booking->bookable_type,
            'bookable_id' => $booking->bookable_id,
            'title' => $booking->title_snapshot,
            'price' => (float) $booking->price_snapshot,
            'quantity' => $booking->quantity,
            'amount' => (float) $booking->amount,
            'attended_mode' => $booking->attended_mode,
            'status' => $booking->status,
            'created_at' => $booking->created_at,
        ];

        if ($withAccess && $booking->status === Booking::STATUS_CONFIRMED) {
            $item = $this->resolveBookable($booking);
            if ($item) {
                if (in_array($booking->attended_mode, ['online', 'both'], true) && !empty($item->link)) {
                    $data['link'] = $item->link;
                }
                if (in_array($booking->attended_mode, ['offline', 'both'], true) && !empty($item->location)) {
                    $data['location'] = $item->location;
                }
            }
        }

        return $data;
    }

    private function resolveBookable(Booking $booking)
    {
        if ($booking->bookable_type === 'class') {
            return DB::table('classes')->where('id', $booking->bookable_id)->first();
        }
        return null;
    }

    public function create_booking(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'bookable_type' => 'required|in:class',
            'bookable_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
            'attended_mode' => 'nullable|in:online,offline',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $class = DB::table('classes')->where('status', 1)->where('id', $request->bookable_id)->first();
        if (!$class) {
            $response['status'] = "false";
            $response['message'] = "Class not found.";
            return response()->json($response);
        }

        $quantity = (int) ($request->quantity ?: 1);

        // Mode resolution — a class configured for 'both' lets the user
        // pick per booking (matches class_detail.dart's existing Online/
        // In-Person toggle); 'online'/'offline' alone has nothing to pick,
        // so we use the class's own mode regardless of what the client
        // sent for attended_mode.
        $classMode = $class->mode ?: 'online';
        if ($classMode === 'both') {
            if (!in_array($request->attended_mode, ['online', 'offline'], true)) {
                $response['status'] = "false";
                $response['message'] = "Please choose Online or In-Person for this class.";
                return response()->json($response);
            }
            $attendedMode = $request->attended_mode;
        } else {
            $attendedMode = $classMode;
        }

        // Capacity check — sum of quantity across bookings that are either
        // already confirmed or currently mid-payment (a pending attempt
        // could still succeed and take a seat). Re-checked again in
        // PaymentController@cascadeToBooking at confirmation time to guard
        // the race where two people are paying for the last seat at once.
        if (!empty($class->max_seats)) {
            $taken = Booking::where('bookable_type', 'class')
                ->where('bookable_id', $class->id)
                ->whereIn('status', [Booking::STATUS_PENDING_PAYMENT, Booking::STATUS_CONFIRMED])
                ->sum('quantity');

            if ($taken + $quantity > $class->max_seats) {
                $response['status'] = "false";
                $response['message'] = "Sorry, this class is full.";
                return response()->json($response);
            }
        }

        $booking = new Booking();
        $booking->user_id = $request->user_id;
        $booking->bookable_type = 'class';
        $booking->bookable_id = $class->id;
        $booking->title_snapshot = $class->title;
        $booking->price_snapshot = $class->price;
        $booking->quantity = $quantity;
        $booking->amount = $class->price * $quantity;
        $booking->attended_mode = $attendedMode;
        $booking->status = Booking::STATUS_PENDING_PAYMENT;
        $booking->booked_at = now();
        $booking->save();

        // Mirrors create_order's response shape — the app follows this by
        // calling create-payment-intent for payable_type=booking,
        // payable_id=this booking's id, then presenting Stripe's
        // PaymentSheet.
        $response['status'] = "true";
        $response['payment_status'] = "pending";
        $response['message'] = "Booking created — awaiting payment";
        $response['data'] = $this->serializeBooking($booking);
        return response()->json($response);
    }

    public function get_bookings(Request $request)
    {
        $rules = ['user_id' => 'required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $bookings = Booking::where('user_id', $request->user_id)
            ->orderBy('id', 'desc')
            ->get();

        $response['status'] = "true";
        $response['message'] = "Bookings";
        $response['data'] = [
            'bookings' => $bookings->map(function ($b) {
                return $this->serializeBooking($b, true);
            })->values(),
        ];
        return response()->json($response);
    }

    public function get_booking_detail(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'booking_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $response['status'] = "false";
            $response['message'] = $this->validationHandle($validator->messages());
            return response()->json($response);
        }

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$booking) {
            $response['status'] = "false";
            $response['message'] = "Booking not found";
            return response()->json($response);
        }

        $response['status'] = "true";
        $response['message'] = "Booking detail";
        $response['data'] = $this->serializeBooking($booking, true);
        return response()->json($response);
    }
}
