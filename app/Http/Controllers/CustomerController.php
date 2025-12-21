<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\ResidentialCollege;
use App\Models\Feedback;

class CustomerController extends Controller
{
    /**
     * Ensure only logged-in customers can access customer pages.
     */
    protected function ensureCustomer(Request $request): void
    {
        if ($request->session()->get('auth_role') !== 'customer') {
            abort(403, 'Only customers can access this area.');
        }
    }

    protected function getCustomerId(Request $request): int
    {
        return (int) $request->session()->get('auth_id');
    }

    public function dashboard(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        $totalBookings = Booking::where('customer_id', $customerId)->count();
        
        $recentBookings = Booking::where('customer_id', $customerId)
            ->with(['car', 'pickupLocation', 'dropoffLocation'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('customer.dashboard', [
            'customer' => $customer,
            'recentBookings' => $recentBookings,
            'totalBookings' => $totalBookings,
        ]);
    }

    public function bookings(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $query = Booking::where('customer_id', $customerId)->with('car');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.bookings.index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status']),
        ]);
    }

    public function bookingsShow(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::with(['car', 'penalties', 'inspections', 'pickupLocation', 'dropoffLocation', 'feedback'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('customer.bookings.show', ['booking' => $booking]);
    }

    public function profile(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        return view('customer.profile', ['customer' => $customer]);
    }

    public function profileUpdate(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'utm_role' => 'nullable|in:student,staff',
        ]);

        $customer->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? $customer->phone,
            'utm_role' => $validated['utm_role'] ?? $customer->utm_role,
        ]);

        // Update session name if changed
        if ($customer->full_name !== session('auth_name')) {
            $request->session()->put('auth_name', $customer->full_name);
        }

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    public function bookingsCancel(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::where('customer_id', $customerId)
            ->findOrFail($id);

        // Only allow cancellation if booking is not yet active or completed
        if (!in_array($booking->status, ['created', 'confirmed'])) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'Cannot cancel a booking that is already active, completed, or cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('customer.bookings.show', $id)
            ->with('success', 'Booking cancelled successfully.');
    }

    public function loyaltyRewards(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        // Get available vouchers (active, not expired, customer has enough stamps)
        $availableVouchers = Voucher::where('is_active', true)
            ->where('expiry_date', '>=', now())
            ->where('min_stamps_required', '<=', $customer->total_stamps ?? 0)
            ->whereDoesntHave('redemptions', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->get();

        // Get redeemed vouchers
        $redeemedVouchers = VoucherRedemption::where('customer_id', $customerId)
            ->with('voucher')
            ->orderBy('redeemed_at', 'desc')
            ->get();

        return view('customer.loyalty-rewards', [
            'customer' => $customer,
            'availableVouchers' => $availableVouchers,
            'redeemedVouchers' => $redeemedVouchers,
        ]);
    }

    public function bookingsSubmitFeedback(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->findOrFail($id);

        // Check if feedback already exists
        if ($booking->feedback) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'Feedback already submitted for this booking.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'reported_issue' => 'boolean',
        ]);

        Feedback::create([
            'booking_id' => $booking->booking_id,
            'customer_id' => $customerId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'reported_issue' => $request->has('reported_issue'),
        ]);

        return redirect()->route('customer.bookings.show', $id)
            ->with('success', 'Thank you for your feedback!');
    }
}
