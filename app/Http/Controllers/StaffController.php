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
use App\Models\Voucher;
use App\Models\MaintenanceRecord;
use App\Models\Feedback;
use App\Models\RentalPhoto;
use App\Models\CancellationRequest;
use App\Models\SupportTicket;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // Calculate revenue statistics
        $totalRevenue = Payment::where('status', 'verified')
            ->where('payment_type', 'rental')
            ->sum('amount');

        $totalDeposits = Payment::where('status', 'verified')
            ->where('payment_type', 'deposit')
            ->sum('amount');

        $monthlyRevenue = Payment::where('status', 'verified')
            ->where('payment_type', 'rental')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

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
            'totalRevenue'        => $totalRevenue,
            'totalDeposits'       => $totalDeposits,
            'monthlyRevenue'      => $monthlyRevenue,
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

        // Paginate results (12 cars per page) with booking counts for delete warnings
        $cars = $query->withCount('bookings')->orderBy('created_at', 'desc')->paginate(12);

        // Get open issue counts for each car (only flagged for maintenance)
        $carIds = $cars->pluck('id')->toArray();
        $issuesCounts = SupportTicket::whereIn('car_id', $carIds)
            ->where('flagged_for_maintenance', true)
            ->whereIn('status', ['open', 'in_progress'])
            ->selectRaw('car_id, COUNT(*) as count')
            ->groupBy('car_id')
            ->pluck('count', 'car_id')
            ->toArray();
        
        // Also check for issues linked through bookings
        $bookingCarIssues = SupportTicket::whereHas('booking', function($q) use ($carIds) {
                $q->whereIn('car_id', $carIds);
            })
            ->where('flagged_for_maintenance', true)
            ->whereIn('status', ['open', 'in_progress'])
            ->with('booking:booking_id,car_id')
            ->get();
        
        foreach ($bookingCarIssues as $issue) {
            if ($issue->booking && $issue->booking->car_id) {
                $carId = $issue->booking->car_id;
                $issuesCounts[$carId] = ($issuesCounts[$carId] ?? 0) + 1;
            }
        }

        return view('staff.cars.index', [
            'cars' => $cars,
            'filters' => $request->only(['status', 'search']),
            'issuesCounts' => $issuesCounts,
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
        
        // Handle nullable current_mileage: Convert empty/null to 0 for new cars (default value)
        // HTML forms may send empty strings, which we've already converted to null before validation
        if (!isset($data['current_mileage']) || $data['current_mileage'] === null) {
            $data['current_mileage'] = 0; // New cars default to 0 mileage
        } else {
            $data['current_mileage'] = (int) $data['current_mileage']; // Ensure it's an integer
        }

        // Handle car image upload
        // Images are stored in storage/app/public/images/cars/ with unique filenames
        try {
            if ($request->hasFile('car_image')) {
                $image = $request->file('car_image');
                // Validate the file was uploaded successfully
                if ($image->isValid()) {
                    // Generate unique filename: timestamp + uniqid + original extension
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('images/cars', $imageName, 'public');
                    
                    if ($imagePath) {
                        $data['image_url'] = $imagePath; // Store relative path
                    } else {
                        return redirect()->back()->withInput()->withErrors(['car_image' => 'Failed to upload image. Please try again.']);
                    }
                }
            } else {
                $data['image_url'] = null; // No image uploaded
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['car_image' => 'Error uploading image: ' . $e->getMessage()]);
        }

        // Create car record in database
        try {
            Car::create($data);
        } catch (\Exception $e) {
            // If car creation fails, delete uploaded image if it was uploaded
            if (isset($data['image_url']) && $data['image_url'] && Storage::disk('public')->exists($data['image_url'])) {
                Storage::disk('public')->delete($data['image_url']);
            }
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create car: ' . $e->getMessage()]);
        }

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
        
        // Get related car issues from support tickets (only flagged for maintenance)
        $carIssues = SupportTicket::with(['customer', 'booking'])
            ->where(function($q) use ($id) {
                $q->where('car_id', $id)
                  ->orWhereHas('booking', function($bq) use ($id) {
                      $bq->where('car_id', $id);
                  });
            })
            ->where('flagged_for_maintenance', true)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('staff.cars.edit', [
            'car' => $car, 
            'locations' => $locations,
            'carIssues' => $carIssues,
        ]);
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

        // Convert empty string to null for nullable integer fields (HTML number inputs send "" when empty)
        $request->merge([
            'current_mileage' => $request->input('current_mileage') === '' ? null : $request->input('current_mileage'),
        ]);

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

        // Handle nullable current_mileage: If null (field was left blank), preserve existing value
        // We've already converted empty strings to null before validation
        if (!isset($data['current_mileage']) || $data['current_mileage'] === null) {
            // Preserve existing mileage value (don't overwrite it if user left field blank)
            $data['current_mileage'] = $car->current_mileage ?? 0;
        } else {
            $data['current_mileage'] = (int) $data['current_mileage']; // Ensure it's an integer
        }

        // Business rule: Auto-set status to maintenance if mileage exceeds service limit
        // This ensures cars requiring service are automatically flagged
        if (isset($data['current_mileage']) && isset($data['service_mileage_limit']) && $data['current_mileage'] >= $data['service_mileage_limit']) {
            $data['status'] = 'maintenance';
        }

        // Handle car image upload/update
        try {
            if ($request->hasFile('car_image')) {
                $image = $request->file('car_image');
                
                // Validate the file was uploaded successfully
                if ($image->isValid()) {
                    // Delete old uploaded image file if it exists (don't delete external URLs)
                    $oldImagePath = $car->getRawOriginal('image_url');
                    if ($oldImagePath && !filter_var($oldImagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }

                    // Upload new image with unique filename
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('images/cars', $imageName, 'public');
                    
                    if ($imagePath) {
                        $data['image_url'] = $imagePath;
                    } else {
                        return redirect()->back()->withInput()->withErrors(['car_image' => 'Failed to upload image. Please try again.']);
                    }
                } else {
                    return redirect()->back()->withInput()->withErrors(['car_image' => 'Invalid image file. Please try again.']);
                }
            } else {
                // If no new image uploaded, preserve existing image_url by explicitly keeping it
                $data['image_url'] = $car->getRawOriginal('image_url');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['car_image' => 'Error uploading image: ' . $e->getMessage()]);
        }

        // Update car record in database
        try {
            $car->update($data);
        } catch (\Exception $e) {
            // If update fails and we uploaded a new image, delete it
            if ($request->hasFile('car_image') && isset($data['image_url']) && Storage::disk('public')->exists($data['image_url'])) {
                Storage::disk('public')->delete($data['image_url']);
            }
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update car: ' . $e->getMessage()]);
        }

        return redirect()->route('staff.cars')->with('success', 'Car updated successfully!');
    }

    /**
     * Delete a car from the fleet
     * Deletes the car's image file if it exists
     * Checks for associated bookings and warns if any exist (bookings will be cascade deleted)
     * 
     * @param Request $request
     * @param int $id Car ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function carsDestroy(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);

        try {
            // Check if car has any bookings (for informational purposes, cascade will handle deletion)
            $bookingCount = $car->bookings()->count();
            
            // Delete car's image file if it exists (don't delete external URLs)
            $imagePath = $car->getRawOriginal('image_url');
            if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            // Delete the car record (bookings will be cascade deleted due to foreign key constraint)
            $carName = $car->brand . ' ' . $car->model . ' (' . $car->plate_number . ')';
            $car->delete();

            $message = 'Car "' . $carName . '" has been deleted successfully.';
            if ($bookingCount > 0) {
                $message .= ' Note: ' . $bookingCount . ' associated booking(s) were also deleted.';
            }

            return redirect()->route('staff.cars')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('staff.cars')->withErrors(['error' => 'Failed to delete car: ' . $e->getMessage()]);
        }
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
            'rentalPhotos',
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

        // Store old status to check if we're transitioning to completed
        $oldStatus = $booking->status;
        $isCompleting = ($oldStatus !== 'completed' && $data['status'] === 'completed');
        $stampsToAward = 0; // Initialize variable

        // Business rule: Cannot confirm booking without inspection photos
        if ($data['status'] === 'confirmed' && $oldStatus === 'created') {
            $hasInspectionPhotos = Inspection::where('booking_id', $booking->booking_id)
                ->where('type', 'before')
                ->whereNotNull('photos')
                ->exists();
            
            if (!$hasInspectionPhotos) {
                return redirect()->back()->with('error', 'Cannot confirm booking without uploading inspection photos first. Please complete the before-pickup inspection.');
            }
        }

        // Update booking status
        $booking->update($data);

        // Auto-award loyalty stamps when booking is completed
        // Business rule: 1 stamp per 9 rental hours (as per schema comment)
        if ($isCompleting) {
            $customer = $booking->customer;
            $rentalHours = $booking->rental_hours ?? 0;
            $stampsToAward = floor($rentalHours / 9); // 1 stamp per 9 hours
            
            if ($stampsToAward > 0) {
                $customer->total_stamps = ($customer->total_stamps ?? 0) + $stampsToAward;
                $customer->total_rental_hours = ($customer->total_rental_hours ?? 0) + $rentalHours;
                $customer->save();
            }
        }

        $successMessage = 'Booking status updated!';
        if ($isCompleting && $stampsToAward > 0) {
            $successMessage .= ' Customer awarded ' . $stampsToAward . ' loyalty stamp(s).';
        }

        return redirect()->back()->with('success', $successMessage);
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
            'amount'         => 'required|numeric|min:0.01',
            'is_installment' => 'sometimes|boolean',
            'installment_count' => 'nullable|integer|min:2|max:12',
            'status'         => 'nullable|in:pending,partially_paid,settled',
            'actual_return_time' => 'nullable|date',
            'pickup_fuel' => 'nullable|numeric|min:0|max:8',
            'return_fuel' => 'nullable|numeric|min:0|max:8',
        ]);

        // Auto-calculate late return penalty
        if ($data['penalty_type'] === 'late' && $request->has('actual_return_time') && $request->actual_return_time) {
            $expectedReturn = $booking->end_datetime;
            $actualReturn = \Carbon\Carbon::parse($request->actual_return_time);
            
            if ($actualReturn > $expectedReturn) {
                $hoursLate = ceil($expectedReturn->diffInHours($actualReturn));
                $calculatedAmount = $hoursLate * 30; // RM30 per hour
                // Use calculated amount if provided, otherwise use form amount
                if ($calculatedAmount > 0) {
                    $data['amount'] = $calculatedAmount;
                }
                $data['description'] = ($data['description'] ?? '') . " ({$hoursLate} hours late)";
            }
        }

        // Auto-calculate fuel shortage penalty
        if ($data['penalty_type'] === 'fuel' && $request->has('pickup_fuel') && $request->has('return_fuel')) {
            $pickupFuel = (float) $request->pickup_fuel;
            $returnFuel = (float) $request->return_fuel;
            if ($pickupFuel > 0 || $returnFuel >= 0) {
                $shortage = max(0, $pickupFuel - $returnFuel);
                $calculatedAmount = $shortage * 10; // RM10 per bar
                // Use calculated amount if provided, otherwise use form amount
                if ($calculatedAmount > 0) {
                    $data['amount'] = $calculatedAmount;
                }
                $data['description'] = ($data['description'] ?? '') . " (Shortage: {$shortage} bars)";
            }
        }

        // Link penalty to booking and set installment flag
        $data['booking_id'] = $booking->booking_id;
        $data['is_installment'] = $request->has('is_installment') && $request->is_installment == '1';
        $data['status'] = $data['status'] ?? 'pending';

        // Create penalty record
        $penalty = Penalty::create($data);

        // If installment is enabled, create installment schedule
        if ($data['is_installment'] && isset($data['installment_count']) && $data['installment_count'] > 1) {
            $installmentAmount = $data['amount'] / $data['installment_count'];
            $dueDate = now()->addMonth();
            
            // Store installment info in description for now (can be moved to separate table later)
            $penalty->update([
                'description' => ($penalty->description ?? '') . " | Installment Plan: {$data['installment_count']} payments of RM " . number_format($installmentAmount, 2) . " monthly"
            ]);
        }

        $message = 'Penalty recorded successfully.';
        if ($data['is_installment']) {
            $message .= ' Installment plan has been set up.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Record an inspection for a booking
     * Used for before-pickup and after-return inspections
     * Tracks fuel level (0-8), odometer reading, photos, and inspection notes
     * Records which staff member performed the inspection
     * Required before confirming a booking
     * 
     * @param Request $request Contains inspection data (type, fuel, odometer, photos, notes)
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsAddInspection(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        // Validate inspection data
        $data = $request->validate([
            'inspection_type'  => 'required|in:before,after,pickup,return,other', // Type of inspection
            'fuel_level'       => 'required|integer|min:0|max:8', // Fuel level (0=empty, 8=full)
            'odometer_reading' => 'required|integer|min:0', // Car mileage at inspection
            'notes'            => 'nullable|string', // Additional notes
            'photos'           => 'nullable|array|max:10', // Inspection photos
            'photos.*'         => 'image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB per photo
        ]);

        // Map pickup/return to before/after for consistency
        $type = $data['inspection_type'];
        if ($type === 'pickup') $type = 'before';
        if ($type === 'return') $type = 'after';

        // Check if inspection already exists for this booking and type
        $existingInspection = Inspection::where('booking_id', $booking->booking_id)
            ->where('type', $type)
            ->first();

        // Handle photo uploads
        $photoPaths = $existingInspection && $existingInspection->photos ? $existingInspection->photos : [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('inspections/' . $booking->booking_id, 'public');
                $photoPaths[] = $path;
            }
        }

        $inspectionData = [
            'booking_id' => $booking->booking_id,
            'car_id' => $booking->car_id,
            'type' => $type,
            'status' => 'completed',
            'fuel_level' => $data['fuel_level'],
            'odometer_reading' => $data['odometer_reading'],
            'mileage_reading' => $data['odometer_reading'],
            'notes' => $data['notes'] ?? null,
            'photos' => $photoPaths,
            'inspected_by' => $request->session()->get('auth_id'),
            'inspected_at' => now(),
            'datetime' => now(),
        ];

        if ($existingInspection) {
            $existingInspection->update($inspectionData);
        } else {
            Inspection::create($inspectionData);
        }

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

        // Calendar Logic
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Calendar Grid Calculation
        $calendarStart = $startDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $calendarEnd = $endDate->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        
        // Eager load staff relationship
        $query = Activity::with('staff');

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // For the calendar, we want all activities that overlap with the visible month
        $activities = $query->where(function($q) use ($calendarStart, $calendarEnd) {
            $q->whereBetween('start_date', [$calendarStart, $calendarEnd])
              ->orWhereBetween('end_date', [$calendarStart, $calendarEnd])
              ->orWhere(function($sq) use ($calendarStart, $calendarEnd) {
                  $sq->where('start_date', '<=', $calendarStart)
                    ->where('end_date', '>=', $calendarEnd);
              });
        })->get();

        return view('staff.activities.index', [
            'activities' => $activities,
            'filters' => $request->only(['status', 'search']),
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
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

    // ========== VOUCHER MANAGEMENT ==========

    /**
     * List all vouchers with filtering and search capabilities
     * Supports filtering by active/inactive status
     * Supports searching by code or description
     * 
     * @param Request $request May contain 'status' and 'search' filter parameters
     * @return \Illuminate\View\View
     */
    public function vouchers(Request $request)
    {
        $this->ensureStaff($request);

        $query = Voucher::query();

        // Filter by active status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('expiry_date', '>=', now());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expiry_date', '<', now());
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 vouchers per page)
        $vouchers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.vouchers.index', [
            'vouchers' => $vouchers,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Display the form to create a new voucher
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function vouchersCreate(Request $request)
    {
        $this->ensureStaff($request);
        return view('staff.vouchers.create');
    }

    /**
     * Store a newly created voucher
     * 
     * @param Request $request Contains voucher data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vouchersStore(Request $request)
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_stamps_required' => 'required|integer|min:0',
            'expiry_date' => 'required|date|after_or_equal:today',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Voucher::create($data);

        return redirect()->route('staff.vouchers')->with('success', 'Voucher created successfully!');
    }

    /**
     * Display the form to edit an existing voucher
     * 
     * @param Request $request
     * @param int $id Voucher ID
     * @return \Illuminate\View\View
     */
    public function vouchersEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $voucher = Voucher::findOrFail($id);
        return view('staff.vouchers.edit', ['voucher' => $voucher]);
    }

    /**
     * Update an existing voucher
     * 
     * @param Request $request Contains updated voucher data
     * @param int $id Voucher ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vouchersUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $voucher = Voucher::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $id . ',voucher_id',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_stamps_required' => 'required|integer|min:0',
            'expiry_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $voucher->update($data);

        return redirect()->route('staff.vouchers')->with('success', 'Voucher updated successfully!');
    }

    /**
     * Delete a voucher
     * 
     * @param Request $request
     * @param int $id Voucher ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vouchersDestroy(Request $request, $id)
    {
        $this->ensureStaff($request);
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('staff.vouchers')->with('success', 'Voucher deleted successfully!');
    }

    // ========== MAINTENANCE RECORDS MANAGEMENT ==========

    /**
     * List all maintenance records for a specific car
     * 
     * @param Request $request
     * @param int $id Car ID
     * @return \Illuminate\View\View
     */
    public function maintenanceRecords(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);
        
        $records = MaintenanceRecord::where('car_id', $id)
            ->orderBy('service_date', 'desc')
            ->paginate(15);

        return view('staff.maintenance.index', [
            'car' => $car,
            'records' => $records,
        ]);
    }

    /**
     * Display the form to create a new maintenance record
     * 
     * @param Request $request
     * @param int $id Car ID
     * @return \Illuminate\View\View
     */
    public function maintenanceCreate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);
        return view('staff.maintenance.create', ['car' => $car]);
    }

    /**
     * Store a newly created maintenance record
     * Updates car's last_service_date and current_mileage if provided
     * 
     * @param Request $request Contains maintenance data
     * @param int $id Car ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenanceStore(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);

        $data = $request->validate([
            'service_date' => 'required|date',
            'description' => 'nullable|string',
            'mileage_at_service' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'update_car_mileage' => 'boolean',
            'update_last_service_date' => 'boolean',
        ]);

        $data['car_id'] = $id;

        // Create maintenance record
        MaintenanceRecord::create($data);

        // Optionally update car's mileage and last service date
        if ($request->has('update_car_mileage')) {
            $car->current_mileage = $data['mileage_at_service'];
        }
        if ($request->has('update_last_service_date')) {
            $car->last_service_date = $data['service_date'];
        }
        
        // If mileage was updated and exceeds service limit, set status to maintenance
        if ($request->has('update_car_mileage') && $car->current_mileage >= $car->service_mileage_limit) {
            $car->status = 'maintenance';
        }
        
        $car->save();

        return redirect()->route('staff.maintenance.index', $id)->with('success', 'Maintenance record created successfully!');
    }

    /**
     * Display the form to edit an existing maintenance record
     * 
     * @param Request $request
     * @param int $carId Car ID
     * @param int $recordId Maintenance Record ID
     * @return \Illuminate\View\View
     */
    public function maintenanceEdit(Request $request, $carId, $recordId)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($carId);
        $record = MaintenanceRecord::where('car_id', $carId)->findOrFail($recordId);
        return view('staff.maintenance.edit', ['car' => $car, 'record' => $record]);
    }

    /**
     * Update an existing maintenance record
     * 
     * @param Request $request Contains updated maintenance data
     * @param int $carId Car ID
     * @param int $recordId Maintenance Record ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenanceUpdate(Request $request, $carId, $recordId)
    {
        $this->ensureStaff($request);
        $record = MaintenanceRecord::where('car_id', $carId)->findOrFail($recordId);

        $data = $request->validate([
            'service_date' => 'required|date',
            'description' => 'nullable|string',
            'mileage_at_service' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $record->update($data);

        return redirect()->route('staff.maintenance.index', $carId)->with('success', 'Maintenance record updated successfully!');
    }

    /**
     * Delete a maintenance record
     * 
     * @param Request $request
     * @param int $carId Car ID
     * @param int $recordId Maintenance Record ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenanceDestroy(Request $request, $carId, $recordId)
    {
        $this->ensureStaff($request);
        $record = MaintenanceRecord::where('car_id', $carId)->findOrFail($recordId);
        $record->delete();

        return redirect()->route('staff.maintenance.index', $carId)->with('success', 'Maintenance record deleted successfully!');
    }

    // ========== FEEDBACK MANAGEMENT ==========

    /**
     * List all customer feedback with filtering capabilities
     * Supports filtering by rating and reported issues
     * Supports searching by customer name or comment
     * 
     * @param Request $request May contain 'rating', 'reported_issue', and 'search' filter parameters
     * @return \Illuminate\View\View
     */
    public function feedbacks(Request $request)
    {
        $this->ensureStaff($request);

        // Check if feedbacks table exists (note: table name is 'feedbacks' plural)
        if (!DB::getSchemaBuilder()->hasTable('feedbacks')) {
            // Create empty paginator for when table doesn't exist
            $emptyPaginator = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                15, // Items per page
                1, // Current page
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            return view('staff.feedbacks.index', [
                'feedbacks' => $emptyPaginator,
                'filters' => [],
                'tableExists' => false,
                'stats' => [
                    'total' => 0,
                    'averageRating' => 0,
                    'reportedIssues' => 0,
                    'ratingDistribution' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                ],
            ]);
        }

        $query = Feedback::with(['customer', 'booking.car']);

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        // Filter by reported issues
        if ($request->has('reported_issue') && $request->reported_issue !== '') {
            $query->where('reported_issue', $request->reported_issue === '1');
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($customerQuery) use ($search) {
                    $customerQuery->where('full_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 feedbacks per page)
        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => Feedback::count(),
            'averageRating' => Feedback::avg('rating') ?? 0,
            'reportedIssues' => Feedback::where('reported_issue', true)->count(),
            'ratingDistribution' => [
                5 => Feedback::where('rating', 5)->count(),
                4 => Feedback::where('rating', 4)->count(),
                3 => Feedback::where('rating', 3)->count(),
                2 => Feedback::where('rating', 2)->count(),
                1 => Feedback::where('rating', 1)->count(),
            ],
        ];

        return view('staff.feedbacks.index', [
            'feedbacks' => $feedbacks,
            'filters' => $request->only(['rating', 'reported_issue', 'search']),
            'stats' => $stats,
            'tableExists' => true,
        ]);
    }

    // ========== CAR LOCATION MANAGEMENT ==========

    /**
     * List all car locations
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function locations(Request $request)
    {
        $this->ensureStaff($request);

        $query = CarLocation::query();

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $locations = $query->orderBy('name', 'asc')->paginate(20);

        return view('staff.locations.index', [
            'locations' => $locations,
            'filters' => $request->only(['type', 'search']),
        ]);
    }

    /**
     * Display the form to create a new location
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function locationsCreate(Request $request)
    {
        $this->ensureStaff($request);
        return view('staff.locations.create');
    }

    /**
     * Store a newly created location
     * 
     * @param Request $request Contains location data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function locationsStore(Request $request)
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:car_locations,name',
            'type' => 'required|in:pickup,dropoff,both',
        ]);

        CarLocation::create($data);

        return redirect()->route('staff.locations')->with('success', 'Location created successfully!');
    }

    /**
     * Display the form to edit an existing location
     * 
     * @param Request $request
     * @param int $id Location ID
     * @return \Illuminate\View\View
     */
    public function locationsEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $location = CarLocation::findOrFail($id);
        return view('staff.locations.edit', ['location' => $location]);
    }

    /**
     * Update an existing location
     * 
     * @param Request $request Contains updated location data
     * @param int $id Location ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function locationsUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $location = CarLocation::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:car_locations,name,' . $id . ',location_id',
            'type' => 'required|in:pickup,dropoff,both',
        ]);

        $location->update($data);

        return redirect()->route('staff.locations')->with('success', 'Location updated successfully!');
    }

    /**
     * Delete a location
     * 
     * @param Request $request
     * @param int $id Location ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function locationsDestroy(Request $request, $id)
    {
        $this->ensureStaff($request);
        $location = CarLocation::findOrFail($id);

        // Check if location is being used in any bookings
        $bookingsCount = Booking::where('pickup_location_id', $id)
            ->orWhere('dropoff_location_id', $id)
            ->count();

        if ($bookingsCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete location. It is being used in ' . $bookingsCount . ' booking(s).');
        }

        $location->delete();

        return redirect()->route('staff.locations')->with('success', 'Location deleted successfully!');
    }

    // ========== EXPORT FUNCTIONALITY ==========

    /**
     * Export bookings to CSV
     * 
     * @param Request $request May contain filters
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function bookingsExport(Request $request)
    {
        $this->ensureStaff($request);

        $query = Booking::with(['car', 'customer']);

        // Apply same filters as index page
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($customerQuery) use ($search) {
                    $customerQuery->where('full_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('car', function($carQuery) use ($search) {
                    $carQuery->where('plate_number', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Booking ID',
                'Customer Name',
                'Customer Email',
                'Car',
                'Plate Number',
                'Pickup Date',
                'Dropoff Date',
                'Hours',
                'Total Amount (RM)',
                'Status',
                'Created At'
            ]);

            // CSV Data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_id,
                    $booking->customer->full_name ?? 'N/A',
                    $booking->customer->email ?? 'N/A',
                    ($booking->car->brand ?? '') . ' ' . ($booking->car->model ?? ''),
                    $booking->car->plate_number ?? 'N/A',
                    $booking->start_datetime?->format('Y-m-d H:i') ?? 'N/A',
                    $booking->end_datetime?->format('Y-m-d H:i') ?? 'N/A',
                    $booking->rental_hours ?? 0,
                    number_format($booking->final_amount ?? 0, 2),
                    ucfirst($booking->status ?? 'N/A'),
                    $booking->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export customers to CSV
     * 
     * @param Request $request May contain filters
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function customersExport(Request $request)
    {
        $this->ensureStaff($request);

        $query = Customer::query();

        // Apply same filters as index page
        if ($request->has('verification_status') && $request->verification_status !== '') {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->has('is_blacklisted') && $request->is_blacklisted !== '') {
            $query->where('is_blacklisted', $request->is_blacklisted === '1');
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'customers_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Customer ID',
                'Full Name',
                'Email',
                'Phone',
                'UTM Role',
                'Verification Status',
                'Is Blacklisted',
                'Deposit Balance (RM)',
                'Total Rental Hours',
                'Loyalty Stamps',
                'Member Since'
            ]);

            // CSV Data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_id,
                    $customer->full_name,
                    $customer->email,
                    $customer->phone ?? 'N/A',
                    ucfirst($customer->utm_role ?? 'N/A'),
                    ucfirst($customer->verification_status ?? 'pending'),
                    $customer->is_blacklisted ? 'Yes' : 'No',
                    number_format($customer->deposit_balance ?? 0, 2),
                    $customer->total_rental_hours ?? 0,
                    $customer->total_stamps ?? 0,
                    $customer->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ========== ADVANCED REPORTING ==========

    /**
     * Display advanced reports and analytics
     * 
     * @param Request $request May contain date range filters
     * @return \Illuminate\View\View
     */
    public function reports(Request $request)
    {
        $this->ensureStaff($request);

        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        // Revenue by month
        $monthlyRevenue = Payment::where('status', 'verified')
            ->where('payment_type', 'rental')
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Bookings by status
        $bookingsByStatus = Booking::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top customers by revenue
        $topCustomers = Customer::with(['bookings' => function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        }])
            ->get()
            ->map(function($customer) {
                $customer->total_revenue = $customer->bookings->sum('final_amount');
                return $customer;
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();

        // Top cars by bookings
        $topCars = Car::withCount(['bookings' => function($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end]);
        }])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Penalty statistics
        $penaltyStats = Penalty::whereBetween('created_at', [$start, $end])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END) as paid_amount
            ')
            ->first();

        return view('staff.reports.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'monthlyRevenue' => $monthlyRevenue,
            'bookingsByStatus' => $bookingsByStatus,
            'topCustomers' => $topCustomers,
            'topCars' => $topCars,
            'penaltyStats' => $penaltyStats,
        ]);
    }

    // ========== DEPOSIT REFUND MANAGEMENT ==========

    /**
     * Process deposit refund for a completed booking
     * 
     * @param Request $request Contains refund decision and amount
     * @param int $id Booking ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookingsProcessRefund(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::with('customer')->findOrFail($id);

        // Only allow refund processing for completed bookings
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'Refunds can only be processed for completed bookings.');
        }

        $data = $request->validate([
            'deposit_decision' => 'required|in:refund,carry_forward,burn',
            'deposit_refund_amount' => 'required|numeric|min:0|max:' . $booking->deposit_amount,
        ]);

        try {
            DB::beginTransaction();

            // Update booking with refund decision
            $booking->update([
                'deposit_decision' => $data['deposit_decision'],
                'deposit_refund_amount' => $data['deposit_refund_amount'],
            ]);

            // If refunding, update customer deposit balance
            if ($data['deposit_decision'] === 'refund' && $data['deposit_refund_amount'] > 0) {
                $customer = $booking->customer;
                $customer->deposit_balance = ($customer->deposit_balance ?? 0) + $data['deposit_refund_amount'];
                $customer->save();

                // Create refund payment record
                Payment::create([
                    'booking_id' => $booking->booking_id,
                    'penalty_id' => null,
                    'amount' => $data['deposit_refund_amount'],
                    'payment_type' => 'refund',
                    'payment_method' => 'bank_transfer', // Default for refunds
                    'receipt_url' => '', // No receipt for refunds
                    'payment_date' => now(),
                    'status' => 'verified', // Auto-verified refunds
                ]);
            } elseif ($data['deposit_decision'] === 'carry_forward' && $data['deposit_refund_amount'] > 0) {
                // Carry forward: add to customer deposit balance
                $customer = $booking->customer;
                $customer->deposit_balance = ($customer->deposit_balance ?? 0) + $data['deposit_refund_amount'];
                $customer->save();
            }
            // 'burn' decision: deposit is forfeited, no action needed

            DB::commit();

            $message = 'Deposit processed successfully. ';
            if ($data['deposit_decision'] === 'refund') {
                $message .= 'RM ' . number_format($data['deposit_refund_amount'], 2) . ' refunded to customer deposit balance.';
            } elseif ($data['deposit_decision'] === 'carry_forward') {
                $message .= 'RM ' . number_format($data['deposit_refund_amount'], 2) . ' carried forward to customer deposit balance.';
            } else {
                $message .= 'Deposit forfeited.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    // ========== ENHANCED PENALTY MANAGEMENT ==========

    /**
     * List all penalties with enhanced filtering and statistics
     * 
     * @param Request $request May contain filters
     * @return \Illuminate\View\View
     */
    public function penaltiesIndex(Request $request)
    {
        $this->ensureStaff($request);

        $query = Penalty::with(['booking.car', 'booking.customer', 'payments']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('penalty_type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('booking.customer', function($customerQuery) use ($search) {
                    $customerQuery->where('full_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('booking.car', function($carQuery) use ($search) {
                    $carQuery->where('plate_number', 'like', "%{$search}%");
                });
            });
        }

        $penalties = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => Penalty::count(),
            'pending' => Penalty::where('status', 'pending')->count(),
            'paid' => Penalty::where('status', 'paid')->count(),
            'total_amount' => Penalty::sum('amount'),
            'paid_amount' => Penalty::where('status', 'paid')->sum('amount'),
            'pending_amount' => Penalty::where('status', 'pending')->sum('amount'),
        ];

        return view('staff.penalties.index', [
            'penalties' => $penalties,
            'stats' => $stats,
            'filters' => $request->only(['status', 'type', 'search']),
        ]);
    }

    // ========== CANCELLATION REQUEST MANAGEMENT ==========

    /**
     * List all cancellation requests with filtering
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function cancellationRequests(Request $request)
    {
        $this->ensureStaff($request);

        $query = CancellationRequest::with(['booking.car', 'customer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by customer name or booking ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Stats
        $stats = [
            'total' => CancellationRequest::count(),
            'pending' => CancellationRequest::where('status', 'pending')->count(),
            'approved' => CancellationRequest::where('status', 'approved')->count(),
            'refunded' => CancellationRequest::where('status', 'refunded')->count(),
            'rejected' => CancellationRequest::where('status', 'rejected')->count(),
        ];

        return view('staff.cancellation-requests.index', [
            'requests' => $requests,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    /**
     * Show details of a specific cancellation request
     * 
     * @param Request $request
     * @param int $id CancellationRequest ID
     * @return \Illuminate\View\View
     */
    public function cancellationRequestsShow(Request $request, $id)
    {
        $this->ensureStaff($request);

        $cancelRequest = CancellationRequest::with([
            'booking.car', 
            'booking.customer', 
            'booking.payments',
            'customer'
        ])->findOrFail($id);

        return view('staff.cancellation-requests.show', [
            'cancelRequest' => $cancelRequest,
        ]);
    }

    /**
     * Process a cancellation request (approve, reject, or mark as refunded)
     * 
     * @param Request $request
     * @param int $id CancellationRequest ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancellationRequestsProcess(Request $request, $id)
    {
        $this->ensureStaff($request);
        $staffId = session('auth_id');

        $cancelRequest = CancellationRequest::with('booking')->findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:approve,reject,refund',
            'staff_notes' => 'nullable|string|max:1000',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_reference' => 'nullable|string|max:100',
        ]);

        $action = $validated['action'];

        if ($action === 'approve') {
            $cancelRequest->update([
                'status' => 'approved',
                'processed_by_staff_id' => $staffId,
                'processed_at' => now(),
                'staff_notes' => $validated['staff_notes'],
            ]);

            // Update booking status to cancelled
            $cancelRequest->booking->update(['status' => 'cancelled']);

            return redirect()->back()->with('success', 'Cancellation request approved. Booking has been cancelled.');

        } elseif ($action === 'reject') {
            $cancelRequest->update([
                'status' => 'rejected',
                'processed_by_staff_id' => $staffId,
                'processed_at' => now(),
                'staff_notes' => $validated['staff_notes'],
            ]);

            return redirect()->back()->with('success', 'Cancellation request rejected.');

        } elseif ($action === 'refund') {
            $cancelRequest->update([
                'status' => 'refunded',
                'processed_by_staff_id' => $staffId,
                'refund_amount' => $validated['refund_amount'],
                'refund_reference' => $validated['refund_reference'],
                'refunded_at' => now(),
                'staff_notes' => $validated['staff_notes'],
            ]);

            return redirect()->back()->with('success', 'Refund has been marked as processed.');
        }

        return redirect()->back()->with('error', 'Invalid action.');
    }

    // ========== SUPPORT TICKET MANAGEMENT ==========

    /**
     * List all support tickets with filtering
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function supportTickets(Request $request)
    {
        $this->ensureStaff($request);

        $query = SupportTicket::with(['customer', 'booking.car', 'car', 'assignedStaff']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('staff.support-tickets.index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'category', 'search']),
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
        $this->ensureStaff($request);

        $ticket = SupportTicket::with([
            'customer',
            'booking.car',
            'car',
            'maintenanceRecord',
            'assignedStaff'
        ])->findOrFail($id);

        // Get all bookings for this customer (for linking)
        $customerBookings = Booking::where('customer_id', $ticket->customer_id)
            ->with('car')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all cars (for linking)
        $cars = Car::orderBy('brand')->orderBy('model')->get();

        // Get all maintenance records (for linking)
        $maintenanceRecords = MaintenanceRecord::with('car')
            ->orderBy('service_date', 'desc')
            ->get();

        // Get all staff members (for assignment)
        $staffMembers = Staff::orderBy('name')->get();

        return view('staff.support-tickets.show', [
            'ticket' => $ticket,
            'customerBookings' => $customerBookings,
            'cars' => $cars,
            'maintenanceRecords' => $maintenanceRecords,
            'staffMembers' => $staffMembers,
        ]);
    }

    /**
     * Update support ticket status and add response
     * 
     * @param Request $request
     * @param int $id Ticket ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function supportTicketsUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $staffId = session('auth_id');

        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'staff_response' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:staff,staff_id',
            'booking_id' => 'nullable|exists:bookings,booking_id',
            'car_id' => 'nullable|exists:cars,id',
            'flagged_for_maintenance' => 'nullable|in:0,1',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to'] ?: null,
            'flagged_for_maintenance' => $validated['flagged_for_maintenance'] ?? false,
        ];

        // Handle staff response (only update if provided, allow clearing)
        if (isset($validated['staff_response'])) {
            $updateData['staff_response'] = $validated['staff_response'] ?: null;
        }

        // Link to booking if provided (auto-links car from booking)
        if (!empty($validated['booking_id'])) {
            $updateData['booking_id'] = $validated['booking_id'];
            $booking = Booking::find($validated['booking_id']);
            if ($booking) {
                $updateData['car_id'] = $booking->car_id; // Auto-link car from booking
            }
        } else {
            // If no booking link, use car_id from form (or clear if empty)
            $updateData['booking_id'] = null;
            $updateData['car_id'] = $validated['car_id'] ?: null;
        }

        // Set resolved_at timestamp if status is resolved
        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $updateData['resolved_at'] = now();
        }

        // Set closed_at timestamp if status is closed
        if ($validated['status'] === 'closed' && !$ticket->closed_at) {
            $updateData['closed_at'] = now();
        }

        $ticket->update($updateData);

        return redirect()->back()->with('success', 'Support ticket updated successfully!');
    }

    /**
     * Display maintenance issues page - shows car issues from support tickets
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function maintenanceIssues(Request $request)
    {
        $this->ensureStaff($request);

        // Get all support tickets that are flagged for maintenance
        $query = SupportTicket::with(['customer', 'car', 'booking.car', 'assignedStaff'])
            ->where('flagged_for_maintenance', true);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, show open and in_progress issues
            $query->whereIn('status', ['open', 'in_progress']);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by car
        if ($request->has('car_id') && $request->car_id !== '') {
            $query->where(function($q) use ($request) {
                $q->where('car_id', $request->car_id)
                  ->orWhereHas('booking', function($bq) use ($request) {
                      $bq->where('car_id', $request->car_id);
                  });
            });
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('car', function($cq) use ($search) {
                      $cq->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%");
                  });
            });
        }

        $issues = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get all cars for filter dropdown
        $cars = Car::orderBy('brand')->orderBy('model')->get();

        // Get issue statistics (only flagged tickets)
        $stats = [
            'total' => SupportTicket::where('flagged_for_maintenance', true)->count(),
            'open' => SupportTicket::where('flagged_for_maintenance', true)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('flagged_for_maintenance', true)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('flagged_for_maintenance', true)->where('status', 'resolved')->count(),
        ];

        return view('staff.car-issues.index', [
            'issues' => $issues,
            'cars' => $cars,
            'filters' => $request->only(['status', 'category', 'car_id', 'search']),
            'stats' => $stats,
        ]);
    }

    /**
     * Mark a car issue as resolved (quick action)
     * 
     * @param Request $request
     * @param int $id Ticket ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function maintenanceIssuesResolve(Request $request, $id)
    {
        $this->ensureStaff($request);
        $staffId = session('auth_id');

        $ticket = SupportTicket::findOrFail($id);
        
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'assigned_to' => $ticket->assigned_to ?? $staffId,
        ]);

        return redirect()->back()->with('success', 'Issue marked as resolved!');
    }

}