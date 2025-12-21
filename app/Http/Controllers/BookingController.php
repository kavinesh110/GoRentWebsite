<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;
use App\Models\CarLocation;
use App\Models\Customer;
use App\Models\Feedback;

/**
 * Handles car booking flow for customers
 * Manages booking form display and submission
 */
class BookingController extends Controller
{
    /**
     * Display the booking form for a specific car
     * Requires customer authentication
     * Shows car details, available locations, and customer reviews
     * 
     * @param Request $request
     * @param int $id Car ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        // Require customer login to book
        if ($request->session()->get('auth_role') !== 'customer') {
            return redirect()->route('login')->with('error', 'Please login as a customer to book a car.');
        }

        try {
            $car = Car::findOrFail($id);
            
            // Check if car has required fields
            if (!$car->brand || !$car->model) {
                abort(404, 'Car information is incomplete');
            }

            // Get customer info
            $customerId = $request->session()->get('auth_id');
            if (!$customerId) {
                return redirect()->route('login')->with('error', 'Please login to book a car.');
            }
            $customer = Customer::findOrFail($customerId);

            // Get available locations from database
            $locations = CarLocation::whereIn('type', ['pickup', 'dropoff', 'both'])
                ->orderBy('name')
                ->get();

            // Get feedbacks for this car (for reviews display)
            // Try to get feedbacks, but handle gracefully if table doesn't exist or query fails
            try {
                $feedbacks = Feedback::whereHas('booking', function($q) use ($car) {
                    $q->where('car_id', $car->id)->where('status', 'completed');
                })->with('customer')->latest()->take(5)->get();
            } catch (\Exception $e) {
                // If feedbacks table doesn't exist or query fails, return empty collection
                $feedbacks = collect([]);
            }
            
            return view('bookings.show', compact('car', 'customer', 'locations', 'feedbacks'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Car not found');
        }
    }

    /**
     * Display create booking page (legacy/alternative route)
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $carName = $request->query('car');
        return view('bookings.create', ['carName' => $carName]);
    }

    /**
     * Process and store a new booking
     * Validates booking data, calculates pricing, creates location records if needed
     * Creates booking with status 'created' (awaiting staff confirmation)
     * 
     * @param Request $request Contains booking form data (car_id, dates, locations, customer info)
     * @return \Illuminate\Http\RedirectResponse Redirects to customer bookings list on success
     */
    public function store(Request $request)
    {
        // Ensure user is logged in as customer
        if ($request->session()->get('auth_role') !== 'customer') {
            return redirect()->route('login')->with('error', 'Please login as a customer to make a booking.');
        }

        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'pickup_location' => 'required|string|max:255',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'dropoff_location' => 'required|string|max:255',
            'dropoff_date' => 'required|date|after_or_equal:pickup_date',
            'dropoff_time' => 'required',
        ]);

        $car = Car::findOrFail($validated['car_id']);
        
        // Calculate rental duration in hours
        $pickup = \Carbon\Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);
        $dropoff = \Carbon\Carbon::parse($validated['dropoff_date'] . ' ' . $validated['dropoff_time']);
        $hours = max(1, $pickup->diffInHours($dropoff)); // Minimum 1 hour rental
        
        // Calculate pricing breakdown
        $basePrice = ($car->base_rate_per_hour ?? 0) * $hours;
        $promoDiscount = 0.00; // TODO: Implement promo code system
        $voucherDiscount = 0.00; // TODO: Implement voucher redemption
        $totalRentalAmount = $basePrice - $promoDiscount - $voucherDiscount;
        $depositAmount = 50.00; // Default deposit amount (RM50)
        $finalAmount = $totalRentalAmount;

        // Get or create location records for pickup and dropoff
        // Locations are created automatically if they don't exist (type: 'both' allows pickup and dropoff)
        $pickupLocation = CarLocation::firstOrCreate(
            ['name' => $validated['pickup_location']],
            ['type' => 'both']
        );
        $dropoffLocation = CarLocation::firstOrCreate(
            ['name' => $validated['dropoff_location']],
            ['type' => 'both']
        );

        // Get authenticated customer ID
        $customerId = $request->session()->get('auth_id');
        if (!$customerId || $request->session()->get('auth_role') !== 'customer') {
            return redirect()->route('login')->with('error', 'Please login to make a booking.');
        }

        $booking = Booking::create([
            'customer_id' => $customerId,
            'car_id' => $validated['car_id'],
            'pickup_location_id' => $pickupLocation->location_id,
            'dropoff_location_id' => $dropoffLocation->location_id,
            'start_datetime' => $pickup,
            'end_datetime' => $dropoff,
            'rental_hours' => $hours,
            'base_price' => $basePrice,
            'promo_discount' => $promoDiscount,
            'voucher_discount' => $voucherDiscount,
            'total_rental_amount' => $totalRentalAmount,
            'deposit_amount' => $depositAmount,
            'deposit_used_amount' => 0.00,
            'deposit_refund_amount' => 0.00,
            'final_amount' => $finalAmount,
            'status' => 'created',
        ]);

        return redirect()->route('customer.bookings')
            ->with('success', 'Booking created successfully! Please wait for confirmation from Hasta staff.');
    }
}