<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\ResidentialCollege;
use App\Models\Feedback;

/**
 * Handles all customer-facing features
 * Includes dashboard, bookings, profile, loyalty rewards, and feedback
 */
class CustomerController extends Controller
{
    /**
     * Ensure only logged-in customers can access customer pages
     * Aborts with 403 error if user is not a customer
     * 
     * @param Request $request
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function ensureCustomer(Request $request): void
    {
        if ($request->session()->get('auth_role') !== 'customer') {
            abort(403, 'Only customers can access this area.');
        }
    }

    /**
     * Get the authenticated customer ID from session
     * 
     * @param Request $request
     * @return int Customer ID
     */
    protected function getCustomerId(Request $request): int
    {
        return (int) $request->session()->get('auth_id');
    }

    /**
     * Display customer dashboard with statistics and recent bookings
     * Shows total bookings, rental hours, deposit balance, loyalty stamps
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
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

    /**
     * List all bookings for the authenticated customer
     * Supports filtering by booking status (created, confirmed, active, completed, cancelled)
     * 
     * @param Request $request May contain 'status' filter parameter
     * @return \Illuminate\View\View
     */
    public function bookings(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Start query with customer's bookings and eager load car relationship
        $query = Booking::where('customer_id', $customerId)->with('car');

        // Apply status filter if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Paginate results (10 bookings per page)
        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.bookings.index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Display detailed view of a specific booking
     * Shows booking details, penalties, inspections, and allows feedback submission
     * Only shows bookings that belong to the authenticated customer
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\View\View
     */
    public function bookingsShow(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Load booking with all related data
        $booking = Booking::with(['car', 'penalties', 'inspections', 'pickupLocation', 'dropoffLocation', 'feedback'])
            ->where('customer_id', $customerId) // Security: Ensure customer can only view their own bookings
            ->findOrFail($id);

        return view('customer.bookings.show', ['booking' => $booking]);
    }

    /**
     * Display customer profile page
     * Shows account information, verification status, documents, and rental statistics
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function profile(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        return view('customer.profile', ['customer' => $customer]);
    }

    /**
     * Update customer profile information
     * Allows updating name, phone, and UTM role
     * Email cannot be changed for security reasons
     * Updates session name if full_name was changed
     * 
     * @param Request $request Contains updated profile data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileUpdate(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        // Validate updateable fields (email is read-only)
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'utm_role' => 'nullable|in:student,staff',
        ]);

        // Update customer profile
        $customer->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? $customer->phone, // Keep existing if not provided
            'utm_role' => $validated['utm_role'] ?? $customer->utm_role,
        ]);

        // Update session name if it changed (so it reflects in navigation)
        if ($customer->full_name !== session('auth_name')) {
            $request->session()->put('auth_name', $customer->full_name);
        }

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Cancel a customer booking
     * Only allows cancellation if booking status is 'created' or 'confirmed'
     * Cannot cancel bookings that are already active, completed, or cancelled
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsCancel(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Find booking and ensure it belongs to this customer
        $booking = Booking::where('customer_id', $customerId)
            ->findOrFail($id);

        // Business rule: Only allow cancellation if booking hasn't started yet
        if (!in_array($booking->status, ['created', 'confirmed'])) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'Cannot cancel a booking that is already active, completed, or cancelled.');
        }

        // Update booking status to cancelled
        $booking->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('customer.bookings.show', $id)
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Display loyalty rewards page
     * Shows available vouchers (that customer has enough stamps for and hasn't redeemed)
     * Shows redeemed vouchers history
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function loyaltyRewards(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        // Get available vouchers that meet all criteria:
        // 1. Voucher is active
        // 2. Voucher hasn't expired
        // 3. Customer has enough stamps to redeem
        // 4. Customer hasn't already redeemed this voucher
        $availableVouchers = Voucher::where('is_active', true)
            ->where('expiry_date', '>=', now())
            ->where('min_stamps_required', '<=', $customer->total_stamps ?? 0)
            ->whereDoesntHave('redemptions', function($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->get();

        // Get all vouchers this customer has previously redeemed
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

    /**
     * Submit feedback/review for a completed booking
     * Only allows feedback on completed bookings
     * Prevents duplicate feedback submissions (one feedback per booking)
     * 
     * @param Request $request Contains feedback data (rating 1-5, comment, reported_issue flag)
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsSubmitFeedback(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Only allow feedback on completed bookings
        $booking = Booking::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->findOrFail($id);

        // Prevent duplicate feedback submissions
        if ($booking->feedback) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'Feedback already submitted for this booking.');
        }

        // Validate feedback data
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5', // Star rating 1-5
            'comment' => 'nullable|string|max:1000',
            'reported_issue' => 'boolean', // Flag if customer reported an issue
        ]);

        // Create feedback record
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
