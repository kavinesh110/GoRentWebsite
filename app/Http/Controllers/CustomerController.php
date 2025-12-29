<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\ResidentialCollege;
use App\Models\Feedback;
use App\Models\Payment;
use App\Models\Car;
use App\Models\CarLocation;
use App\Models\RentalPhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Note: 'feedback' is excluded from eager loading as the table may not exist
        $withRelations = ['car', 'penalties', 'inspections', 'pickupLocation', 'dropoffLocation', 'payments', 'rentalPhotos'];
        
        // Only eager load feedback if the table exists
        $feedbackTableExists = DB::getSchemaBuilder()->hasTable('feedback');
        if ($feedbackTableExists) {
            $withRelations[] = 'feedback';
        }
        
        $booking = Booking::with($withRelations)
            ->where('customer_id', $customerId) // Security: Ensure customer can only view their own bookings
            ->findOrFail($id);

        // If feedback table doesn't exist, set feedback to null to prevent lazy loading errors
        if (!$feedbackTableExists) {
            $booking->setRelation('feedback', null);
        }

        return view('customer.bookings.show', ['booking' => $booking]);
    }

    /**
     * Show payment page for a specific booking
     * Displays QR code placeholder and payment receipt upload form
     *
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\View\View
     */
    public function bookingsPayment(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $pending = $request->session()->get('pending_booking');
        if (!$pending || ($pending['customer_id'] ?? null) !== $customerId) {
            return redirect()->route('customer.bookings')
                ->with('error', 'No pending booking found. Please start your booking again.');
        }

        $car = Car::findOrFail($pending['car_id']);

        $pickup = \Carbon\Carbon::parse($pending['pickup_date'] . ' ' . $pending['pickup_time']);
        $dropoff = \Carbon\Carbon::parse($pending['dropoff_date'] . ' ' . $pending['dropoff_time']);

        $pickupFormatted = $pickup->format('d M Y, H:i');
        $dropoffFormatted = $dropoff->format('d M Y, H:i');
        $durationHours = $pending['hours'];
        $totalToPay = ($pending['deposit_amount'] ?? 0) + ($pending['total_rental_amount'] ?? 0);

        return view('customer.bookings.payment', [
            'car' => $car,
            'pending' => $pending,
            'pickupFormatted' => $pickupFormatted,
            'dropoffFormatted' => $dropoffFormatted,
            'durationHours' => $durationHours,
            'totalToPay' => $totalToPay,
        ]);
    }

    /**
     * Handle payment receipt submission for a pending booking
     * Creates the booking and an associated deposit payment in one step
     */
    public function bookingsPaymentSubmit(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $pending = $request->session()->get('pending_booking');
        if (!$pending || ($pending['customer_id'] ?? null) !== $customerId) {
            return redirect()->route('customer.bookings')
                ->with('error', 'No pending booking found. Please start your booking again.');
        }

        $validated = $request->validate([
            'payment_type' => 'required|in:deposit', // booking is created only with deposit payment
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'payment_date' => 'required|date',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Wrap everything in a database transaction for atomicity
        try {
            DB::beginTransaction();

            // Handle receipt upload
            $receipt = $request->file('receipt');
            $receiptName = time() . '_' . uniqid() . '.' . $receipt->getClientOriginalExtension();
            $receiptPath = $receipt->storeAs('images/payments', $receiptName, 'public');

            // Rebuild Carbon dates
            $pickup = \Carbon\Carbon::parse($pending['pickup_date'] . ' ' . $pending['pickup_time']);
            $dropoff = \Carbon\Carbon::parse($pending['dropoff_date'] . ' ' . $pending['dropoff_time']);

            // Ensure locations exist
            $pickupLocation = CarLocation::firstOrCreate(
                ['name' => $pending['pickup_location']],
                ['type' => 'both']
            );
            $dropoffLocation = CarLocation::firstOrCreate(
                ['name' => $pending['dropoff_location']],
                ['type' => 'both']
            );

            // Create booking record now that deposit receipt is submitted
            $booking = Booking::create([
                'customer_id' => $customerId,
                'car_id' => $pending['car_id'],
                'pickup_location_id' => $pickupLocation->location_id,
                'dropoff_location_id' => $dropoffLocation->location_id,
                'start_datetime' => $pickup,
                'end_datetime' => $dropoff,
                'rental_hours' => $pending['hours'],
                'base_price' => $pending['base_price'],
                'promo_discount' => $pending['promo_discount'] ?? 0.00,
                'voucher_discount' => $pending['voucher_discount'] ?? 0.00,
                'total_rental_amount' => $pending['total_rental_amount'],
                'deposit_amount' => $pending['deposit_amount'],
                'deposit_used_amount' => 0.00,
                'deposit_refund_amount' => 0.00,
                'final_amount' => $pending['final_amount'],
                'status' => 'created',
            ]);

            // Create voucher redemption record if voucher was used
            if (!empty($pending['voucher_id']) && ($pending['voucher_discount'] ?? 0) > 0) {
                VoucherRedemption::create([
                    'voucher_id' => $pending['voucher_id'],
                    'customer_id' => $customerId,
                    'booking_id' => $booking->booking_id,
                    'discount_amount' => $pending['voucher_discount'],
                    'redeemed_at' => now(),
                ]);
            }

            // Create payment record linked to this booking
            Payment::create([
                'booking_id' => $booking->booking_id,
                'penalty_id' => null,
                'amount' => $validated['amount'],
                'payment_type' => $validated['payment_type'],
                'payment_method' => $validated['payment_method'],
                'receipt_url' => $receiptPath,
                'payment_date' => $validated['payment_date'],
                'status' => 'pending', // Staff must verify
            ]);

            // Commit transaction
            DB::commit();

            // Clear pending booking data from session
            $request->session()->forget('pending_booking');

            return redirect()->route('customer.bookings.show', $booking->booking_id)
                ->with('success', 'Deposit receipt uploaded. Your booking has been created and is pending verification by Hasta staff.');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            // Log error for debugging
            Log::error('Booking creation failed: ' . $e->getMessage(), [
                'exception' => $e,
                'pending_data' => $pending,
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create booking. Please try again. If the problem persists, contact support.']);
        }
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
     * Upload or replace customer verification documents (IC, UTM ID, licence)
     */
    public function profileDocumentsUpdate(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'ic_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'utmid_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'license_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Helper closure to handle single document field
        $handleUpload = function (string $field, string $column, string $subdir) use ($request, $customer) {
            if (!$request->hasFile($field)) {
                return;
            }
            $file = $request->file($field);
            $oldPath = $customer->getRawOriginal($column);
            if ($oldPath && !filter_var($oldPath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("images/customers/{$subdir}", $name, 'public');
            $customer->{$column} = $path;
        };

        $handleUpload('ic_document', 'ic_url', 'ic');
        $handleUpload('utmid_document', 'utmid_url', 'utmid');
        $handleUpload('license_document', 'license_url', 'licence');

        $customer->save();

        return redirect()->route('customer.profile')
            ->with('success', 'Documents updated successfully!');
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

    /**
     * Upload payment receipt for a booking
     * Allows customers to upload payment receipts for deposits or rental payments
     * Creates a pending payment record that staff can verify
     * 
     * @param Request $request Contains payment data and receipt file
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsUploadReceipt(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::where('customer_id', $customerId)
            ->findOrFail($id);

        $validated = $request->validate([
            'payment_type' => 'required|in:deposit,rental',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'payment_date' => 'required|date',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Handle receipt upload
        $receipt = $request->file('receipt');
        $receiptName = time() . '_' . uniqid() . '.' . $receipt->getClientOriginalExtension();
        $receiptPath = $receipt->storeAs('images/payments', $receiptName, 'public');

        // Create payment record with pending status
        Payment::create([
            'booking_id' => $booking->booking_id,
            'penalty_id' => null,
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'payment_method' => $validated['payment_method'],
            'receipt_url' => $receiptPath,
            'payment_date' => $validated['payment_date'],
            'status' => 'pending', // Staff will verify
        ]);

        return redirect()->route('customer.bookings.show', $id)
            ->with('success', 'Payment receipt uploaded successfully! Staff will verify your payment shortly.');
    }

    /**
     * Upload rental photos for an active booking
     * Allows customers to upload photos during active bookings (before/after, damage, etc.)
     * 
     * @param Request $request Contains photo file and metadata
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsUploadPhoto(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::where('customer_id', $customerId)
            ->findOrFail($id);

        // Only allow photo uploads for active or completed bookings
        if (!in_array($booking->status, ['active', 'completed'])) {
            return redirect()->back()
                ->with('error', 'Photos can only be uploaded for active or completed bookings.');
        }

        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'photo_type' => 'required|in:before,after,key,damage,other',
            'taken_at' => 'required|date',
        ]);

        // Handle photo upload
        $photo = $request->file('photo');
        $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

        // Create rental photo record
        RentalPhoto::create([
            'booking_id' => $booking->booking_id,
            'uploaded_by_user_id' => $customerId,
            'uploaded_by_role' => 'customer',
            'photo_type' => $validated['photo_type'],
            'photo_url' => $photoPath,
            'taken_at' => $validated['taken_at'],
        ]);

        return redirect()->back()
            ->with('success', 'Photo uploaded successfully!');
    }
}
