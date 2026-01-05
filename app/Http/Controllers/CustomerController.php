<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\Feedback;
use App\Models\Payment;
use App\Models\Car;
use App\Models\CarLocation;
use App\Models\RentalPhoto;
use App\Models\SupportTicket;
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

        // Start query with customer's bookings and eager load car relationship, penalties, and feedback
        $query = Booking::where('customer_id', $customerId)->with(['car', 'penalties', 'feedback']);

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
        $withRelations = ['car', 'penalties.payments', 'inspections', 'pickupLocation', 'dropoffLocation', 'payments', 'rentalPhotos', 'feedback'];
        
        $booking = Booking::with($withRelations)
            ->where('customer_id', $customerId) // Security: Ensure customer can only view their own bookings
            ->findOrFail($id);

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
            'bank_name' => 'required|string|max:100',
            'account_holder_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
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
                'bank_name' => $validated['bank_name'],
                'account_holder_name' => $validated['account_holder_name'],
                'account_number' => $validated['account_number'],
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
            'college_name' => 'nullable|string|max:100',
        ]);

        // Update customer profile
        $customer->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? $customer->phone, // Keep existing if not provided
            'utm_role' => $validated['utm_role'] ?? $customer->utm_role,
            'college_name' => $validated['college_name'] ?? $customer->college_name,
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
     * Update customer password
     * Requires current password verification before allowing change
     * 
     * @param Request $request Contains current_password, new_password, new_password_confirmation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profilePasswordUpdate(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);
        $customer = Customer::findOrFail($customerId);

        // Validate password fields
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!password_verify($validated['current_password'], $customer->password_hash)) {
            return redirect()->route('customer.profile')
                ->with('error', 'Current password is incorrect.');
        }

        // Update password
        $customer->update([
            'password_hash' => password_hash($validated['new_password'], PASSWORD_BCRYPT),
        ]);

        return redirect()->route('customer.profile')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Show the cancellation request form
     * Customer must fill out reason, bank details for refund, and optional proof
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\View\View
     */
    public function bookingsCancelForm(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Find booking and ensure it belongs to this customer
        $booking = Booking::with(['car', 'pickupLocation', 'dropoffLocation', 'cancellationRequest'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        // Business rule: Only allow cancellation if booking hasn't started yet
        if (!in_array($booking->status, ['created', 'confirmed'])) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'Cannot cancel a booking that is already active, completed, or cancelled.');
        }

        // Check if cancellation request already exists
        if ($booking->hasCancellationRequest()) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'A cancellation request for this booking already exists.');
        }

        // Business rule: Cancellation requests are only available 24 hours before pickup time
        if ($booking->start_datetime) {
            $hoursUntilPickup = now()->diffInHours($booking->start_datetime, false);
            
            if ($hoursUntilPickup < 24) {
                return redirect()->route('customer.bookings.show', $id)
                    ->with('error', 'Cancellation requests are only available at least 24 hours before the pickup time. Since your pickup is scheduled for ' . $booking->start_datetime->format('d M Y, H:i') . ', cancellation is no longer available. Please proceed with your booking as scheduled.');
            }
        }

        // Get reason types for dropdown
        $reasonTypes = \App\Models\CancellationRequest::getReasonTypes();

        return view('customer.bookings.cancel', [
            'booking' => $booking,
            'reasonTypes' => $reasonTypes,
        ]);
    }

    /**
     * Submit a cancellation request
     * Creates a new CancellationRequest record with customer's details
     * Booking status is NOT changed immediately - staff must approve first
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsCancelSubmit(Request $request, $id)
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

        // Check if cancellation request already exists
        if ($booking->hasCancellationRequest()) {
            return redirect()->route('customer.bookings.show', $id)
                ->with('error', 'A cancellation request for this booking already exists.');
        }

        // Business rule: Cancellation requests are only available 24 hours before pickup time
        if ($booking->start_datetime) {
            $hoursUntilPickup = now()->diffInHours($booking->start_datetime, false);
            
            if ($hoursUntilPickup < 24) {
                return redirect()->route('customer.bookings.show', $id)
                    ->with('error', 'Cancellation requests are only available at least 24 hours before the pickup time. Since your pickup is scheduled for ' . $booking->start_datetime->format('d M Y, H:i') . ', cancellation is no longer available. Please proceed with your booking as scheduled.');
            }
        }

        // Validate the form
        $validated = $request->validate([
            'reason_type' => 'required|in:change_of_plans,found_alternative,financial_reasons,emergency,vehicle_issue,service_issue,other',
            'reason_details' => 'nullable|string|max:1000',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:100',
            'proof_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'agree_terms' => 'required|accepted',
        ]);

        // Handle proof document upload
        $proofDocumentPath = null;
        if ($request->hasFile('proof_document')) {
            $file = $request->file('proof_document');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofDocumentPath = $file->storeAs('images/cancellation-proofs', $fileName, 'public');
        }

        // Create cancellation request
        \App\Models\CancellationRequest::create([
            'booking_id' => $booking->booking_id,
            'customer_id' => $customerId,
            'reason_type' => $validated['reason_type'],
            'reason_details' => $validated['reason_details'],
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_holder' => $validated['bank_account_holder'],
            'proof_document_url' => $proofDocumentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('customer.bookings.show', $id)
            ->with('success', 'Your cancellation request has been submitted successfully. Our staff will review it and process your refund within 3-5 business days.');
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

        // Only allow feedback on completed or deposit_returned bookings
        $booking = Booking::where('customer_id', $customerId)
            ->whereIn('status', ['completed', 'deposit_returned'])
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
            'bank_name' => 'required|string|max:100',
            'account_holder_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
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
            'bank_name' => $validated['bank_name'],
            'account_holder_name' => $validated['account_holder_name'],
            'account_number' => $validated['account_number'],
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

        // Allow photo uploads for verified, confirmed, active, or completed bookings (for phases 3 and 4)
        if (!in_array($booking->status, ['verified', 'confirmed', 'active', 'completed'])) {
            return redirect()->back()
                ->with('error', 'Photos can only be uploaded for verified, confirmed, active, or completed bookings.');
        }

        // Check if this is a combined return photos upload (both keys in car and parking location)
        if ($request->hasFile('keys_in_car_photo') || $request->hasFile('parking_location_photo')) {
            // Combined upload for return photos
        $validated = $request->validate([
                'keys_in_car_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'parking_location_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'taken_at' => 'required|date',
            ]);

            $photosUploaded = 0;
            $messages = [];

            // Handle keys in car photo upload
            if ($request->hasFile('keys_in_car_photo')) {
                $photo = $request->file('keys_in_car_photo');
                $photoName = time() . '_keys_car_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

                $existingPhoto = RentalPhoto::where('booking_id', $booking->booking_id)
                    ->where('photo_type', 'key')
                    ->first();

                if ($existingPhoto) {
                    if (Storage::disk('public')->exists($existingPhoto->photo_url)) {
                        Storage::disk('public')->delete($existingPhoto->photo_url);
                    }
                    $existingPhoto->update([
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                    ]);
                } else {
                    RentalPhoto::create([
                        'booking_id' => $booking->booking_id,
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                        'photo_type' => 'key',
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                    ]);
                }
                $photosUploaded++;
                $messages[] = 'Keys in car photo uploaded';
            }

            // Handle parking location photo upload
            if ($request->hasFile('parking_location_photo')) {
                $photo = $request->file('parking_location_photo');
                $photoName = time() . '_parking_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

                $existingPhoto = RentalPhoto::where('booking_id', $booking->booking_id)
                    ->where('photo_type', 'parking')
                    ->first();

                if ($existingPhoto) {
                    if (Storage::disk('public')->exists($existingPhoto->photo_url)) {
                        Storage::disk('public')->delete($existingPhoto->photo_url);
                    }
                    $existingPhoto->update([
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                    ]);
                } else {
                    RentalPhoto::create([
                        'booking_id' => $booking->booking_id,
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                        'photo_type' => 'parking',
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                    ]);
                }
                $photosUploaded++;
                $messages[] = 'Parking location photo uploaded';
            }

            // Reload booking with relationships
            $booking->load('rentalPhotos');

            $message = implode(' and ', $messages) . ' successfully!';
            
            // Check if phase 4 is now complete (both return photos uploaded)
            $keysInCarPhoto = $booking->rentalPhotos->where('photo_type', 'key')->first();
            $parkingPhoto = $booking->rentalPhotos->where('photo_type', 'parking')->first();
            
            if ($keysInCarPhoto && $parkingPhoto) {
                // Both return photos uploaded - mark booking as completed
                if ($booking->status !== 'completed') {
                    $booking->status = 'completed';
                    $booking->save();
                    $message .= ' Booking return process is complete! Thank you for using Hasta GoRent.';
                } else {
                    $message .= ' Both return photos are uploaded.';
                }
            } elseif ($photosUploaded > 0) {
                if (!$keysInCarPhoto) {
                    $message .= ' Please also upload the keys in car photo.';
                } elseif (!$parkingPhoto) {
                    $message .= ' Please also upload the parking location photo.';
                }
            }

            return redirect()->route('customer.bookings.show', $booking->booking_id)
                ->with('success', $message);
        }

        // Check if this is a combined pickup photos upload (both agreement and keys)
        if ($request->hasFile('agreement_photo') || $request->hasFile('keys_photo')) {
            // Combined upload for pickup photos
            $validated = $request->validate([
                'agreement_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'keys_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'taken_at' => 'required|date',
            ]);

            $photosUploaded = 0;
            $messages = [];

            // Handle agreement photo upload
            if ($request->hasFile('agreement_photo')) {
                $photo = $request->file('agreement_photo');
                $photoName = time() . '_agreement_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

                $existingPhoto = RentalPhoto::where('booking_id', $booking->booking_id)
                    ->where('photo_type', 'agreement')
                    ->first();

                if ($existingPhoto) {
                    if (Storage::disk('public')->exists($existingPhoto->photo_url)) {
                        Storage::disk('public')->delete($existingPhoto->photo_url);
                    }
                    $existingPhoto->update([
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                    ]);
                } else {
                    RentalPhoto::create([
                        'booking_id' => $booking->booking_id,
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                        'photo_type' => 'agreement',
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                    ]);
                }
                $photosUploaded++;
                $messages[] = 'Signed agreement photo uploaded';
            }

            // Handle keys photo upload
            if ($request->hasFile('keys_photo')) {
                $photo = $request->file('keys_photo');
                $photoName = time() . '_keys_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

                $existingPhoto = RentalPhoto::where('booking_id', $booking->booking_id)
                    ->where('photo_type', 'pickup')
                    ->first();

                if ($existingPhoto) {
                    if (Storage::disk('public')->exists($existingPhoto->photo_url)) {
                        Storage::disk('public')->delete($existingPhoto->photo_url);
                    }
                    $existingPhoto->update([
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                    ]);
                } else {
                    RentalPhoto::create([
                        'booking_id' => $booking->booking_id,
                        'uploaded_by_user_id' => $customerId,
                        'uploaded_by_role' => 'customer',
                        'photo_type' => 'pickup',
                        'photo_url' => $photoPath,
                        'taken_at' => $validated['taken_at'],
                    ]);
                }
                $photosUploaded++;
                $messages[] = 'Receiving keys photo uploaded';
            }

            // Mark agreement as signed if agreement photo was uploaded
            if ($request->hasFile('agreement_photo') && !$booking->agreement_signed_at) {
                $booking->agreement_signed_at = now();
                $booking->save();
            }

            // Reload booking with relationships
            $booking->load('rentalPhotos');

            $message = implode(' and ', $messages) . ' successfully!';
            
            // Check if phase 3 is now complete
            if ($booking->isPhase3Complete()) {
                $message .= ' Phase 3 is now complete. You can proceed to Phase 4.';
            } elseif ($photosUploaded > 0) {
                $agreementPhoto = $booking->rentalPhotos->where('photo_type', 'agreement')->first();
                $keysPhoto = $booking->rentalPhotos->where('photo_type', 'pickup')->first();
                
                if (!$agreementPhoto) {
                    $message .= ' Please also upload the signed agreement photo.';
                } elseif (!$keysPhoto) {
                    $message .= ' Please also upload the receiving keys photo.';
                }
            }

            return redirect()->route('customer.bookings.show', $booking->booking_id)
                ->with('success', $message);
        }

        // Original single photo upload logic (for return photos, etc.)
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_type' => 'required|in:before,after,key,damage,other,agreement,pickup,parking',
            'taken_at' => 'required|date',
        ]);

        // Handle photo upload
        $photo = $request->file('photo');
        $photoName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs('images/rental-photos', $photoName, 'public');

        // Check if a photo of this type already exists and update it, or create new
        $existingPhoto = RentalPhoto::where('booking_id', $booking->booking_id)
            ->where('photo_type', $validated['photo_type'])
            ->first();

        if ($existingPhoto) {
            // Delete old photo file
            if (Storage::disk('public')->exists($existingPhoto->photo_url)) {
                Storage::disk('public')->delete($existingPhoto->photo_url);
            }
            
            // Update existing photo record
            $existingPhoto->update([
                'photo_url' => $photoPath,
                'taken_at' => $validated['taken_at'],
                'uploaded_by_user_id' => $customerId,
                'uploaded_by_role' => 'customer',
            ]);
        } else {
            // Create new rental photo record
        RentalPhoto::create([
            'booking_id' => $booking->booking_id,
            'uploaded_by_user_id' => $customerId,
            'uploaded_by_role' => 'customer',
            'photo_type' => $validated['photo_type'],
            'photo_url' => $photoPath,
            'taken_at' => $validated['taken_at'],
        ]);
        }

        // If uploading an agreement photo, also mark the agreement as signed
        if ($validated['photo_type'] === 'agreement' && !$booking->agreement_signed_at) {
            $booking->agreement_signed_at = now();
            $booking->save();
        }

        // Auto-complete booking when Phase 4 is complete (return photos uploaded)
        // Check if this is a return photo and if Phase 4 requirements are met
        if (in_array($validated['photo_type'], ['after', 'key', 'parking']) && $booking->status === 'active') {
            // Check if Phase 4 is complete (has return photos)
            $hasReturnPhotos = $booking->rentalPhotos()
                ->whereIn('photo_type', ['after', 'key', 'parking'])
                ->exists();
            
            if ($hasReturnPhotos) {
                // Phase 4 is complete - automatically change status to completed
                $booking->status = 'completed';
                $booking->save();
                
                // Auto-award loyalty stamps
                $customer = $booking->customer;
                $rentalHours = $booking->rental_hours ?? 0;
                $stampsToAward = floor($rentalHours / 9); // 1 stamp per 9 hours
                
                if ($stampsToAward > 0) {
                    $customer->total_stamps = ($customer->total_stamps ?? 0) + $stampsToAward;
                    $customer->total_rental_hours = ($customer->total_rental_hours ?? 0) + $rentalHours;
                    $customer->save();
                }
            }
        }

        // Reload booking with relationships to ensure photos are available in the view
        $booking->load('rentalPhotos');

        $message = 'Photo uploaded successfully!';
        
        // Check if phase 3 is now complete
        if ($booking->isPhase3Complete()) {
            $message .= ' Phase 3 is now complete. You can proceed to Phase 4.';
        }

        return redirect()->route('customer.bookings.show', $booking->booking_id)
            ->with('success', $message);
    }

    /**
     * Download the rental agreement PDF
     * Generates and downloads the agreement.pdf file
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\Http\Response
     */
    public function bookingsDownloadAgreement(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        $booking = Booking::with(['customer', 'car', 'pickupLocation', 'dropoffLocation'])->where('customer_id', $customerId)
            ->findOrFail($id);

        // Only allow download for verified, confirmed, or active bookings
        if (!in_array($booking->status, ['verified', 'confirmed', 'active'])) {
            return redirect()->back()
                ->with('error', 'Agreement can only be downloaded for verified, confirmed, or active bookings.');
        }

        // Generate HTML content for the agreement
        $htmlContent = $this->generateAgreementPDF($booking);

        // For now, return HTML that browsers can print/save as PDF
        // In production, use a library like dompdf or tcpdf for proper PDF generation
        // For simplicity, we'll return HTML with instructions to save as PDF
        return response($htmlContent)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="rental-agreement-' . $booking->booking_id . '.html"');
    }

    /**
     * Generate agreement PDF content
     * Creates a simple PDF with booking details and terms
     * 
     * @param Booking $booking
     * @return string PDF content
     */
    private function generateAgreementPDF($booking)
    {
        // Generate HTML content for the agreement
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rental Agreement</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; line-height: 1.6; }
        h1 { color: #cb3737; text-align: center; border-bottom: 3px solid #cb3737; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .info-section { margin: 20px 0; }
        .info-row { margin: 10px 0; }
        .label { font-weight: bold; display: inline-block; width: 200px; }
        .terms { margin: 20px 0; padding: 15px; background: #f5f5f5; border-left: 4px solid #cb3737; }
        .signature { margin-top: 50px; }
        .signature-line { border-top: 1px solid #333; width: 300px; margin-top: 50px; }
    </style>
</head>
<body>
    <h1>RENTAL AGREEMENT</h1>
    
    <div class="info-section">
        <div class="info-row"><span class="label">Booking ID:</span> ' . $booking->booking_id . '</div>
        <div class="info-row"><span class="label">Date:</span> ' . now()->format('d M Y') . '</div>
    </div>
    
    <h2>CUSTOMER INFORMATION</h2>
    <div class="info-section">
        <div class="info-row"><span class="label">Name:</span> ' . htmlspecialchars($booking->customer->full_name) . '</div>
        <div class="info-row"><span class="label">Email:</span> ' . htmlspecialchars($booking->customer->email) . '</div>
        <div class="info-row"><span class="label">Phone:</span> ' . htmlspecialchars($booking->customer->phone_number ?? 'N/A') . '</div>
    </div>
    
    <h2>VEHICLE INFORMATION</h2>
    <div class="info-section">
        <div class="info-row"><span class="label">Vehicle:</span> ' . htmlspecialchars($booking->car->brand . ' ' . $booking->car->model) . '</div>
        <div class="info-row"><span class="label">Plate Number:</span> ' . htmlspecialchars($booking->car->plate_number) . '</div>
    </div>
    
    <h2>RENTAL PERIOD</h2>
    <div class="info-section">
        <div class="info-row"><span class="label">Pickup:</span> ' . $booking->start_datetime->format('d M Y, H:i') . ' at ' . htmlspecialchars($booking->pickupLocation->name ?? 'N/A') . '</div>
        <div class="info-row"><span class="label">Return:</span> ' . $booking->end_datetime->format('d M Y, H:i') . ' at ' . htmlspecialchars($booking->dropoffLocation->name ?? 'N/A') . '</div>
        <div class="info-row"><span class="label">Duration:</span> ' . $booking->rental_hours . ' hours</div>
    </div>
    
    <h2>FINANCIAL DETAILS</h2>
    <div class="info-section">
        <div class="info-row"><span class="label">Base Price:</span> RM ' . number_format($booking->base_price, 2) . '</div>
        <div class="info-row"><span class="label">Deposit:</span> RM ' . number_format($booking->deposit_amount, 2) . '</div>
        <div class="info-row"><span class="label">Final Amount:</span> RM ' . number_format($booking->final_amount, 2) . '</div>
    </div>
    
    <h2>TERMS AND CONDITIONS</h2>
    <div class="terms">
        <ol>
            <li>The renter agrees to return the vehicle in the same condition as received.</li>
            <li>The renter is responsible for any damage, traffic violations, or fines during the rental period.</li>
            <li>The vehicle must be returned with the same fuel level as pickup.</li>
            <li>Late returns will incur additional charges as per the hourly rate (RM30/hour).</li>
            <li>The deposit will be refunded after vehicle inspection upon return.</li>
            <li>Smoking and pets are not allowed in the vehicle.</li>
            <li>The renter must have a valid driving license at all times.</li>
        </ol>
    </div>
    
    <div class="signature">
        <p>By signing this agreement, the customer acknowledges that they have read, understood, and agree to all terms and conditions stated above.</p>
        <div class="signature-line"></div>
        <p><strong>Customer Signature:</strong></p>
        <p><strong>Date:</strong> ' . now()->format('d M Y') . '</p>
    </div>
</body>
</html>';

        // For now, return HTML that can be converted to PDF by browser
        // In production, use a library like dompdf or tcpdf
        // For simplicity, we'll return HTML and let the browser handle it
        // Or use a service to convert HTML to PDF
        
        return $html;
    }

    /**
     * List all support tickets for the authenticated customer
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function supportTickets(Request $request)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Get customer's support tickets
        $query = SupportTicket::where('customer_id', $customerId);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Paginate results (10 tickets per page)
        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('customer.support-tickets.index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Display detailed view of a support ticket
     * 
     * @param Request $request
     * @param int $id Ticket ID
     * @return \Illuminate\View\View
     */
    public function supportTicketsShow(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Get ticket and ensure it belongs to this customer
        $ticket = SupportTicket::with(['booking.car', 'car', 'assignedStaff'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('customer.support-tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Handle penalty payment submission from customer
     * Allows customers to pay penalties with receipt upload
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsPenaltyPay(Request $request, $id)
    {
        $this->ensureCustomer($request);
        $customerId = $this->getCustomerId($request);

        // Get booking and ensure it belongs to this customer
        $booking = Booking::with(['penalties.payments'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        $validated = $request->validate([
            'penalty_id' => 'required|exists:penalties,penalty_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'payment_date' => 'required|date',
            'receipt' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Verify penalty belongs to this booking
        $penalty = $booking->penalties->find($validated['penalty_id']);
        if (!$penalty) {
            return redirect()->back()->with('error', 'Invalid penalty selected.');
        }

        // Check if penalty is already settled
        if ($penalty->status === 'settled') {
            return redirect()->back()->with('error', 'This penalty has already been settled.');
        }

        // Calculate remaining amount
        $paidAmount = $penalty->payments ? $penalty->payments->where('status', 'verified')->sum('amount') : 0;
        $remainingAmount = max(0, $penalty->amount - $paidAmount);

        // Validate payment amount doesn't exceed remaining
        if ($validated['amount'] > $remainingAmount) {
            return redirect()->back()->with('error', 'Payment amount cannot exceed the remaining penalty amount of RM ' . number_format($remainingAmount, 2));
        }

        // Handle receipt upload
        $receipt = $request->file('receipt');
        $receiptName = time() . '_penalty_' . uniqid() . '.' . $receipt->getClientOriginalExtension();
        $receiptPath = $receipt->storeAs('images/payments', $receiptName, 'public');

        // Create payment record
        Payment::create([
            'booking_id' => $booking->booking_id,
            'penalty_id' => $penalty->penalty_id,
            'amount' => $validated['amount'],
            'payment_type' => 'penalty',
            'payment_method' => $validated['payment_method'],
            'receipt_url' => $receiptPath,
            'payment_date' => $validated['payment_date'],
            'status' => 'pending', // Staff will verify
        ]);

        // Update penalty status based on payment
        $newPaidAmount = $paidAmount + $validated['amount'];
        if ($newPaidAmount >= $penalty->amount) {
            $penalty->status = 'settled';
        } elseif ($newPaidAmount > 0) {
            $penalty->status = 'partially_paid';
        }
        $penalty->save();

        return redirect()->route('customer.bookings.show', $booking->booking_id)
            ->with('success', 'Penalty payment submitted successfully! Your payment receipt has been uploaded and is pending staff verification.');
    }
}