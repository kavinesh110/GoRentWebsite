<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\CarLocation;
use App\Models\Penalty;
use App\Models\Inspection;
use App\Models\Activity;
use App\Models\Payment;

/**
 * Handles all staff/admin functionality
 * Includes car management, booking management, customer management, and activity management
 */
class StaffController extends Controller
{
    /**
     * Ensure only logged-in staff can access staff pages
     * Aborts with 403 error if user is not staff
     * 
     * @param Request $request
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function ensureStaff(Request $request): void
    {
        if ($request->session()->get('auth_role') !== 'staff') {
            abort(403, 'Only Hasta staff can access this area.');
        }
    }

    /**
     * Display staff dashboard with key statistics
     * Shows total cars, available cars, active bookings, and total customers
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $this->ensureStaff($request);

        $stats = [
            'totalCars'           => Car::count(),
            'availableCars'       => Car::where('status', 'available')->count(),
            'carsInUse'           => Car::where('status', 'in_use')->count(),
            'carsInMaintenance'   => Car::where('status', 'maintenance')->count(),
            'pendingBookings'     => Booking::where('status', 'created')->count(),
            'activeBookings'      => Booking::where('status', 'active')->count(),
            'completedBookings'   => Booking::where('status', 'completed')->count(),
            'totalCustomers'      => Customer::count(),
            'pendingVerifications' => Customer::where('verification_status', 'pending')->count(),
            'blacklistedCustomers' => Customer::where('is_blacklisted', true)->count(),
        ];

        // Get recent bookings that need attention
        $recentBookings = Booking::with(['car', 'customer'])
            ->whereIn('status', ['created', 'active'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get pending customer verifications
        $pendingCustomers = Customer::where('verification_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('staff.dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'pendingCustomers' => $pendingCustomers,
        ]);
    }

    // ========== CAR MANAGEMENT ==========
    
    /**
     * List all cars with filtering and search capabilities
     * Supports filtering by status (available, in_use, maintenance)
     * Supports searching by brand, model, or plate number
     * 
     * @param Request $request May contain 'status' and 'search' filter parameters
     * @return \Illuminate\View\View
     */
    public function cars(Request $request)
    {
        $this->ensureStaff($request);

        $query = Car::query();
        
        // Filter by car status (available, in_use, maintenance)
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality: search in brand, model, or plate number
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        // Paginate results (12 cars per page)
        $cars = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('staff.cars.index', [
            'cars' => $cars,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display the form to create a new car
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function carsCreate(Request $request)
    {
        $this->ensureStaff($request);
        $locations = CarLocation::all();
        return view('staff.cars.create', ['locations' => $locations]);
    }

    /**
     * Store a newly created car
     * Handles image upload and stores car information
     * Validates all car data including unique plate number
     * 
     * @param Request $request Contains car form data and optional image file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function carsStore(Request $request)
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:cars,plate_number',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'fuel_type' => 'required|in:petrol,diesel,hybrid,ev,other',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'base_rate_per_hour' => 'required|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance',
            'current_mileage' => 'nullable|integer|min:0',
            'service_mileage_limit' => 'required|integer|min:0',
            'last_service_date' => 'nullable|date',
            'car_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gps_enabled' => 'boolean',
        ]);

        // Process checkbox and default values
        $data['gps_enabled'] = $request->has('gps_enabled');
        $data['current_mileage'] = $data['current_mileage'] ?? 0;

        // Handle car image upload
        // Images are stored in storage/app/public/images/cars/ with unique filenames
        if ($request->hasFile('car_image')) {
            $image = $request->file('car_image');
            // Generate unique filename: timestamp + uniqid + original extension
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/cars', $imageName, 'public');
            $data['image_url'] = $imagePath; // Store relative path
        } else {
            $data['image_url'] = null; // No image uploaded
        }

        // Create car record in database
        Car::create($data);

        return redirect()->route('staff.cars')->with('success', 'Car added successfully!');
    }

    /**
     * Display the form to edit an existing car
     * 
     * @param Request $request
     * @param int $id Car ID
     * @return \Illuminate\View\View
     */
    public function carsEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);
        $locations = CarLocation::all();
        return view('staff.cars.edit', ['car' => $car, 'locations' => $locations]);
    }

    /**
     * Update an existing car
     * Handles image upload (replaces old image if new one is uploaded)
     * Auto-updates car status to 'maintenance' if mileage exceeds service limit
     * 
     * @param Request $request Contains updated car data and optional new image
     * @param int $id Car ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function carsUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);

        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:cars,plate_number,' . $id,
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'fuel_type' => 'required|in:petrol,diesel,hybrid,ev,other',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'base_rate_per_hour' => 'required|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance',
            'current_mileage' => 'nullable|integer|min:0',
            'service_mileage_limit' => 'required|integer|min:0',
            'last_service_date' => 'nullable|date',
            'car_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gps_enabled' => 'boolean',
        ]);

        // Process checkbox
        $data['gps_enabled'] = $request->has('gps_enabled');

        // Business rule: Auto-set status to maintenance if mileage exceeds service limit
        // This ensures cars requiring service are automatically flagged
        if ($data['current_mileage'] >= $data['service_mileage_limit']) {
            $data['status'] = 'maintenance';
        }

        // Handle car image upload/update
        if ($request->hasFile('car_image')) {
            // Delete old uploaded image file if it exists (don't delete external URLs)
            $oldImagePath = $car->getRawOriginal('image_url');
            if ($oldImagePath && !filter_var($oldImagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            // Upload new image with unique filename
            $image = $request->file('car_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/cars', $imageName, 'public');
            $data['image_url'] = $imagePath;
        }
        // If no new image uploaded, existing image_url is preserved (not overwritten)

        $car->update($data);

        return redirect()->route('staff.cars')->with('success', 'Car updated successfully!');
    }

    // ========== BOOKING MANAGEMENT ==========
    
    /**
     * List all bookings with filtering and search
     * Supports filtering by booking status
     * Supports searching by customer name/email or car plate number
     * 
     * @param Request $request May contain 'status' and 'search' filter parameters
     * @return \Illuminate\View\View
     */
    public function bookings(Request $request)
    {
        $this->ensureStaff($request);

        // Eager load car and customer relationships for performance
        $query = Booking::with(['car', 'customer']);

        // Filter by booking status (created, confirmed, active, completed, cancelled)
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality: search in customer name/email or car plate number
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('car', function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 bookings per page)
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.bookings.index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display detailed view of a specific booking
     * Shows booking details, penalties, inspections
     * Allows staff to update status, add penalties, and record inspections
     * 
     * @param Request $request
     * @param int $id Booking ID
     * @return \Illuminate\View\View
     */
    public function bookingsShow(Request $request, $id)
    {
        $this->ensureStaff($request);

        // Eager load all related data for display
        $booking = Booking::with([
            'car',
            'customer',
            'penalties',
            'inspections' => function ($q) {
                $q->orderBy('datetime', 'asc'); // Order inspections chronologically
            },
            'payments' => function ($q) {
                $q->orderBy('payment_date', 'desc'); // Order payments by date (newest first)
            },
        ])->findOrFail($id);

        return view('staff.bookings.show', ['booking' => $booking]);
    }

    /**
     * Update booking status
     * Allows staff to change booking status through the workflow
     * Status options: created, confirmed, active, completed, cancelled
     * 
     * @param Request $request Contains new status
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsUpdateStatus(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        // Validate status value
        $data = $request->validate([
            'status' => 'required|in:created,confirmed,active,completed,cancelled',
        ]);

        // Business rule: booking cannot be confirmed/activated until deposit payment is verified
        if (in_array($data['status'], ['confirmed', 'active'])) {
            $hasVerifiedDeposit = $booking->payments()
                ->where('payment_type', 'deposit')
                ->where('status', 'verified')
                ->exists();

            if (!$hasVerifiedDeposit) {
                return redirect()->back()->with('error', 'Cannot change status to ' . $data['status'] . ' before a deposit payment is verified.');
            }
        }

        // Update booking status
        $booking->update($data);

        return redirect()->back()->with('success', 'Booking status updated!');
    }

    /**
     * Add a penalty to a booking
     * Records penalties for late returns, fuel issues, damage, accidents, etc.
     * Supports installment payments (penalty can be paid in parts)
     * 
     * @param Request $request Contains penalty data (type, amount, description, status)
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsAddPenalty(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        $data = $request->validate([
            'penalty_type'   => 'required|in:late,fuel,damage,accident,other',
            'description'    => 'nullable|string',
            'amount'         => 'required|numeric|min:0',
            'is_installment' => 'sometimes|boolean',
            'status'         => 'required|in:pending,partially_paid,settled',
        ]);

        // Link penalty to booking and set installment flag
        $data['booking_id'] = $booking->booking_id;
        $data['is_installment'] = $request->has('is_installment');

        // Create penalty record
        Penalty::create($data);

        return redirect()->back()->with('success', 'Penalty recorded for this booking.');
    }

    /**
     * Record an inspection for a booking
     * Used for pickup and return inspections
     * Tracks fuel level (0-8), odometer reading, and inspection notes
     * Records which staff member performed the inspection
     * 
     * @param Request $request Contains inspection data (type, datetime, fuel, odometer, notes)
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsAddInspection(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        // Validate inspection data
        $data = $request->validate([
            'inspection_type'  => 'required|in:pickup,return,other', // Type of inspection
            'datetime'         => 'required|date', // When inspection was performed
            'fuel_level'       => 'required|integer|min:0|max:8', // Fuel level (0=empty, 8=full)
            'odometer_reading' => 'required|integer|min:0', // Car mileage at inspection
            'notes'            => 'nullable|string', // Additional notes
        ]);

        // Link inspection to booking and record inspecting staff member
        $data['booking_id'] = $booking->booking_id;
        $data['inspected_by'] = $request->session()->get('auth_id'); // Staff who performed inspection

        // Create inspection record
        Inspection::create($data);

        return redirect()->back()->with('success', 'Inspection recorded for this booking.');
    }

    // ========== CUSTOMER MANAGEMENT ==========
    
    /**
     * List all customers with filtering and search
     * Supports filtering by verification status (pending, approved, rejected)
     * Supports filtering by blacklist status
     * Supports searching by customer name or email
     * 
     * @param Request $request May contain 'verification_status', 'blacklisted', and 'search' parameters
     * @return \Illuminate\View\View
     */
    public function customers(Request $request)
    {
        $this->ensureStaff($request);

        $query = Customer::query();

        // Filter by verification status (pending, approved, rejected)
        if ($request->has('verification_status') && $request->verification_status !== '') {
            $query->where('verification_status', $request->verification_status);
        }

        // Filter by blacklist status (show only blacklisted or only non-blacklisted)
        if ($request->has('blacklisted')) {
            $query->where('is_blacklisted', $request->blacklisted === '1');
        }

        // Search functionality: search in customer name or email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 customers per page)
        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.customers.index', [
            'customers' => $customers,
            'filters' => $request->only(['verification_status', 'blacklisted', 'search']),
        ]);
    }

    /**
     * Display detailed view of a specific customer
     * Shows customer information, verification status, booking history
     * Allows staff to verify/blacklist customers
     * 
     * @param Request $request
     * @param int $id Customer ID
     * @return \Illuminate\View\View
     */
    public function customersShow(Request $request, $id)
    {
        $this->ensureStaff($request);
        // Eager load bookings relationship for display
        $customer = Customer::with('bookings')->findOrFail($id);
        return view('staff.customers.show', ['customer' => $customer]);
    }

    /**
     * Update customer verification status
     * Staff can approve, reject, or leave customer as pending
     * Records which staff member verified and when
     * 
     * @param Request $request Contains new verification_status
     * @param int $id Customer ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function customersVerify(Request $request, $id)
    {
        $this->ensureStaff($request);
        $customer = Customer::findOrFail($id);

        // Validate verification status
        $data = $request->validate([
            'verification_status' => 'required|in:pending,approved,rejected',
        ]);

        // Record who verified and when
        $data['verified_by'] = $request->session()->get('auth_id');
        $data['verified_at'] = now();

        // Update customer verification status
        $customer->update($data);

        return redirect()->back()->with('success', 'Customer verification status updated!');
    }

    /**
     * Blacklist or remove blacklist status for a customer
     * When blacklisting, requires a reason and records the date
     * When removing blacklist, clears reason and date
     * Blacklisted customers cannot login
     * 
     * @param Request $request Contains is_blacklisted flag and optional blacklist_reason
     * @param int $id Customer ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function customersBlacklist(Request $request, $id)
    {
        $this->ensureStaff($request);
        $customer = Customer::findOrFail($id);

        // Validate blacklist data (reason required if blacklisting)
        $data = $request->validate([
            'is_blacklisted' => 'required|boolean',
            'blacklist_reason' => 'required_if:is_blacklisted,1|nullable|string|max:500',
        ]);

        // Set blacklist date if blacklisting, clear if removing blacklist
        if ($data['is_blacklisted']) {
            $data['blacklist_since'] = now(); // Record when customer was blacklisted
        } else {
            $data['blacklist_reason'] = null; // Clear reason when removing blacklist
            $data['blacklist_since'] = null; // Clear date when removing blacklist
        }

        // Update customer blacklist status
        $customer->update($data);

        $message = $data['is_blacklisted'] ? 'Customer blacklisted.' : 'Customer removed from blacklist.';
        return redirect()->back()->with('success', $message);
    }

    // ========== ACTIVITY MANAGEMENT ==========
    
    /**
     * List all activities/promotions with filtering and search
     * Supports filtering by status (active or expired)
     * Supports searching by title or description
     * 
     * @param Request $request May contain 'status' and 'search' filter parameters
     * @return \Illuminate\View\View
     */
    public function activities(Request $request)
    {
        $this->ensureStaff($request);

        // Eager load staff relationship (to show who created each activity)
        $query = Activity::with('staff');

        // Filter by activity status
        // Active: end_date >= today (still running)
        // Expired: end_date < today (past activities)
        if ($request->has('status') && $request->status === 'active') {
            $query->where('end_date', '>=', now());
        } elseif ($request->has('status') && $request->status === 'expired') {
            $query->where('end_date', '<', now());
        }

        // Search functionality: search in activity title or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 activities per page)
        $activities = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('staff.activities.index', [
            'activities' => $activities,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display the form to create a new activity/promotion
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function activitiesCreate(Request $request)
    {
        $this->ensureStaff($request);
        return view('staff.activities.create');
    }

    /**
     * Store a newly created activity/promotion
     * Records which staff member created the activity
     * Validates that end_date is after or equal to start_date
     * 
     * @param Request $request Contains activity data (title, description, start_date, end_date)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activitiesStore(Request $request)
    {
        $this->ensureStaff($request);

        // Validate activity data
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'activity_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // End date must be >= start date
        ]);

        // Record which staff member created this activity
        $data['created_by'] = $request->session()->get('auth_id');

        // Handle optional hero image upload
        if ($request->hasFile('activity_image')) {
            $image = $request->file('activity_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/activities', $imageName, 'public');
            $data['image_url'] = $imagePath;
        }

        // Create activity record
        Activity::create($data);

        return redirect()->route('staff.activities')->with('success', 'Activity created successfully!');
    }

    /**
     * Display the form to edit an existing activity
     * 
     * @param Request $request
     * @param int $id Activity ID
     * @return \Illuminate\View\View
     */
    public function activitiesEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);
        return view('staff.activities.edit', ['activity' => $activity]);
    }

    /**
     * Update an existing activity/promotion
     * Validates that end_date is after or equal to start_date
     * 
     * @param Request $request Contains updated activity data
     * @param int $id Activity ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activitiesUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);

        // Validate activity data
        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'activity_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // End date must be >= start date
        ]);

        // Handle optional hero image upload/update
        if ($request->hasFile('activity_image')) {
            $oldImagePath = $activity->getRawOriginal('image_url');
            if ($oldImagePath && !filter_var($oldImagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            $image = $request->file('activity_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/activities', $imageName, 'public');
            $data['image_url'] = $imagePath;
        }

        // Update activity record
        $activity->update($data);

        return redirect()->route('staff.activities')->with('success', 'Activity updated successfully!');
    }

    /**
     * Delete an activity/promotion
     * Permanently removes the activity from the database
     * 
     * @param Request $request
     * @param int $id Activity ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activitiesDestroy(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);
        
        // Delete activity record
        $activity->delete();

        return redirect()->route('staff.activities')->with('success', 'Activity deleted successfully!');
    }

    // ========== PAYMENT MANAGEMENT ==========
    
    /**
     * Store a new payment for a booking
     * Allows staff to record payments (deposits, rental fees, penalties, refunds)
     * Handles receipt upload if provided
     * 
     * @param Request $request Contains payment data (type, amount, method, date, receipt)
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentsStore(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'payment_type' => 'required|in:deposit,rental,penalty,refund,installment',
            'penalty_id' => 'nullable|exists:penalties,penalty_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,other',
            'payment_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Handle receipt upload if provided
        $receiptUrl = null;
        if ($request->hasFile('receipt')) {
            $receipt = $request->file('receipt');
            $receiptName = time() . '_' . uniqid() . '.' . $receipt->getClientOriginalExtension();
            $receiptPath = $receipt->storeAs('images/payments', $receiptName, 'public');
            $receiptUrl = $receiptPath;
        }

        // Create payment record
        Payment::create([
            'booking_id' => $booking->booking_id,
            'penalty_id' => $validated['penalty_id'] ?? null,
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'payment_method' => $validated['payment_method'],
            'receipt_url' => $receiptUrl,
            'payment_date' => $validated['payment_date'],
            'status' => 'pending', // Default to pending, staff can verify later
        ]);

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    /**
     * Verify a payment
     * Changes payment status from 'pending' to 'verified'
     * 
     * @param Request $request
     * @param int $id Payment ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentsVerify(Request $request, $id)
    {
        $this->ensureStaff($request);
        $payment = Payment::findOrFail($id);

        $payment->update([
            'status' => 'verified',
        ]);

        return redirect()->back()->with('success', 'Payment verified successfully!');
    }
}
