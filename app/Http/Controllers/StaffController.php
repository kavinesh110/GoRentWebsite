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

class StaffController extends Controller
{
    /**
     * Simple guard to ensure only logged-in staff can access staff pages.
     */
    protected function ensureStaff(Request $request): void
    {
        if ($request->session()->get('auth_role') !== 'staff') {
            abort(403, 'Only Hasta staff can access this area.');
        }
    }

    public function dashboard(Request $request)
    {
        $this->ensureStaff($request);

        $stats = [
            'totalCars'       => Car::count(),
            'availableCars'   => Car::where('status', 'available')->count(),
            'activeBookings'  => Booking::whereIn('status', ['created', 'ongoing'])->count(),
            'totalCustomers'  => Customer::count(),
        ];

        return view('staff.dashboard', [
            'stats' => $stats,
        ]);
    }

    // ========== CAR MANAGEMENT ==========
    public function cars(Request $request)
    {
        $this->ensureStaff($request);

        $query = Car::query();
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by brand/model/plate
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%");
            });
        }

        $cars = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('staff.cars.index', [
            'cars' => $cars,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function carsCreate(Request $request)
    {
        $this->ensureStaff($request);
        $locations = CarLocation::all();
        return view('staff.cars.create', ['locations' => $locations]);
    }

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

        $data['gps_enabled'] = $request->has('gps_enabled');
        $data['current_mileage'] = $data['current_mileage'] ?? 0;

        // Handle image upload
        if ($request->hasFile('car_image')) {
            $image = $request->file('car_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/cars', $imageName, 'public');
            $data['image_url'] = $imagePath;
        } else {
            $data['image_url'] = null;
        }

        Car::create($data);

        return redirect()->route('staff.cars')->with('success', 'Car added successfully!');
    }

    public function carsEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $car = Car::findOrFail($id);
        $locations = CarLocation::all();
        return view('staff.cars.edit', ['car' => $car, 'locations' => $locations]);
    }

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

        $data['gps_enabled'] = $request->has('gps_enabled');

        // Auto-update status if mileage exceeds service limit
        if ($data['current_mileage'] >= $data['service_mileage_limit']) {
            $data['status'] = 'maintenance';
        }

        // Handle image upload
        if ($request->hasFile('car_image')) {
            // Delete old image if exists (use raw original value for file operations)
            $oldImagePath = $car->getRawOriginal('image_url');
            if ($oldImagePath && !filter_var($oldImagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            $image = $request->file('car_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/cars', $imageName, 'public');
            $data['image_url'] = $imagePath;
        }
        // If no new image uploaded, keep existing image_url (don't set it)

        $car->update($data);

        return redirect()->route('staff.cars')->with('success', 'Car updated successfully!');
    }

    // ========== BOOKING MANAGEMENT ==========
    public function bookings(Request $request)
    {
        $this->ensureStaff($request);

        $query = Booking::with(['car', 'customer']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by customer name/email or car plate
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('car', function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.bookings.index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function bookingsShow(Request $request, $id)
    {
        $this->ensureStaff($request);

        $booking = Booking::with([
            'car',
            'customer',
            'penalties',
            'inspections' => function ($q) {
                $q->orderBy('datetime', 'asc');
            },
        ])->findOrFail($id);

        return view('staff.bookings.show', ['booking' => $booking]);
    }

    public function bookingsUpdateStatus(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:created,confirmed,ongoing,completed,cancelled',
        ]);

        $booking->update($data);

        return redirect()->back()->with('success', 'Booking status updated!');
    }

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

        $data['booking_id'] = $booking->booking_id;
        $data['is_installment'] = $request->has('is_installment');

        Penalty::create($data);

        return redirect()->back()->with('success', 'Penalty recorded for this booking.');
    }

    public function bookingsAddInspection(Request $request, $id)
    {
        $this->ensureStaff($request);
        $booking = Booking::findOrFail($id);

        $data = $request->validate([
            'inspection_type'  => 'required|in:pickup,return,other',
            'datetime'         => 'required|date',
            'fuel_level'       => 'required|integer|min:0|max:8',
            'odometer_reading' => 'required|integer|min:0',
            'notes'            => 'nullable|string',
        ]);

        $data['booking_id'] = $booking->booking_id;
        $data['inspected_by'] = $request->session()->get('auth_id');

        Inspection::create($data);

        return redirect()->back()->with('success', 'Inspection recorded for this booking.');
    }

    // ========== CUSTOMER MANAGEMENT ==========
    public function customers(Request $request)
    {
        $this->ensureStaff($request);

        $query = Customer::query();

        // Filter by verification status
        if ($request->has('verification_status') && $request->verification_status !== '') {
            $query->where('verification_status', $request->verification_status);
        }

        // Filter by blacklist
        if ($request->has('blacklisted')) {
            $query->where('is_blacklisted', $request->blacklisted === '1');
        }

        // Search by name/email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.customers.index', [
            'customers' => $customers,
            'filters' => $request->only(['verification_status', 'blacklisted', 'search']),
        ]);
    }

    public function customersShow(Request $request, $id)
    {
        $this->ensureStaff($request);
        $customer = Customer::with('bookings')->findOrFail($id);
        return view('staff.customers.show', ['customer' => $customer]);
    }

    public function customersVerify(Request $request, $id)
    {
        $this->ensureStaff($request);
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'verification_status' => 'required|in:pending,approved,rejected',
        ]);

        $data['verified_by'] = $request->session()->get('auth_id');
        $data['verified_at'] = now();

        $customer->update($data);

        return redirect()->back()->with('success', 'Customer verification status updated!');
    }

    public function customersBlacklist(Request $request, $id)
    {
        $this->ensureStaff($request);
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'is_blacklisted' => 'required|boolean',
            'blacklist_reason' => 'required_if:is_blacklisted,1|nullable|string|max:500',
        ]);

        if ($data['is_blacklisted']) {
            $data['blacklist_since'] = now();
        } else {
            $data['blacklist_reason'] = null;
            $data['blacklist_since'] = null;
        }

        $customer->update($data);

        $message = $data['is_blacklisted'] ? 'Customer blacklisted.' : 'Customer removed from blacklist.';
        return redirect()->back()->with('success', $message);
    }

    // ========== ACTIVITY MANAGEMENT ==========
    public function activities(Request $request)
    {
        $this->ensureStaff($request);

        $query = Activity::with('staff');

        // Filter by active/expired
        if ($request->has('status') && $request->status === 'active') {
            $query->where('end_date', '>=', now());
        } elseif ($request->has('status') && $request->status === 'expired') {
            $query->where('end_date', '<', now());
        }

        // Search by title
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('staff.activities.index', [
            'activities' => $activities,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function activitiesCreate(Request $request)
    {
        $this->ensureStaff($request);
        return view('staff.activities.create');
    }

    public function activitiesStore(Request $request)
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $data['created_by'] = $request->session()->get('auth_id');

        Activity::create($data);

        return redirect()->route('staff.activities')->with('success', 'Activity created successfully!');
    }

    public function activitiesEdit(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);
        return view('staff.activities.edit', ['activity' => $activity]);
    }

    public function activitiesUpdate(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $activity->update($data);

        return redirect()->route('staff.activities')->with('success', 'Activity updated successfully!');
    }

    public function activitiesDestroy(Request $request, $id)
    {
        $this->ensureStaff($request);
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return redirect()->route('staff.activities')->with('success', 'Activity deleted successfully!');
    }
}
