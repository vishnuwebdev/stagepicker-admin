<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin visibility into class bookings (Store > Bookings in the sidebar).
 * Read-only besides `updateStatus`, which mirrors admin/OrderController —
 * cancelling here is bookkeeping only, no refund is triggered (refunds are
 * handled manually in the Stripe dashboard, same as orders/payment
 * transactions — see PaymentTransactionController's doc comment).
 */
class BookingController extends AdminController
{
    public function list(Request $request)
    {
        $query = Booking::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bookable_id')) {
            $query->where('bookable_id', $request->bookable_id);
        }

        $bookings = $query->orderBy('id', 'desc')->get();

        $userIds = $bookings->pluck('user_id')->unique()->filter()->values();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $data['bookings'] = $bookings;
        $data['users'] = $users;
        $data['filters'] = $request->only(['status', 'bookable_id']);
        return \View::make('admin/bookings/list', $data);
    }

    public function view($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return redirect('admin/bookings')->with('error', 'Booking not found');
        }

        $class = $booking->bookable_type === 'class'
            ? DB::table('classes')->where('id', $booking->bookable_id)->first()
            : null;

        $data['booking'] = $booking;
        $data['user'] = User::find($booking->user_id);
        $data['class'] = $class;
        return \View::make('admin/bookings/view', $data);
    }

    /**
     * Per-class attendee list — who's actually enrolled, confirmed or
     * still mid-payment. Linked from the class admin views (Store >
     * Classes > view).
     */
    public function attendees($classId)
    {
        $class = DB::table('classes')->where('id', $classId)->first();
        if (!$class) {
            return redirect('admin/classes')->with('error', 'Class not found');
        }

        $bookings = Booking::where('bookable_type', 'class')
            ->where('bookable_id', $classId)
            ->orderBy('id', 'desc')
            ->get();

        $userIds = $bookings->pluck('user_id')->unique()->filter()->values();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $data['class'] = $class;
        $data['bookings'] = $bookings;
        $data['users'] = $users;
        $data['seatsTaken'] = $bookings->whereIn('status', [Booking::STATUS_PENDING_PAYMENT, Booking::STATUS_CONFIRMED])->sum('quantity');
        return \View::make('admin/bookings/attendees', $data);
    }

    /**
     * Bookkeeping-only status change — cancelling a booking here does not
     * touch Stripe or issue a refund (see class doc comment). It just
     * frees the seat (create_booking's capacity check only counts
     * pending_payment + confirmed).
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return redirect('admin/bookings')->with('error', 'Booking not found');
        }

        if (!in_array($request->status, [Booking::STATUS_PENDING_PAYMENT, Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED], true)) {
            return redirect('admin/bookings/' . $id)->with('error', 'Invalid status.');
        }

        $booking->status = $request->status;
        if ($request->status === Booking::STATUS_CANCELLED) {
            $booking->cancelled_at = now();
        }
        $booking->save();

        return redirect('admin/bookings/' . $id)->with('success', 'Booking updated successfully');
    }
}
