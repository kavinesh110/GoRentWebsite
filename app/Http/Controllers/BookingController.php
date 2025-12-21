<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;
use App\Models\CarLocation;
use App\Models\Customer;

class BookingController extends Controller
{
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
            $customer = \App\Models\Customer::findOrFail($customerId);

            // Get available locations from database
            $locations = \App\Models\CarLocation::whereIn('type', ['pickup', 'dropoff', 'both'])
                ->orderBy('name')
                ->get();

            // Get feedbacks for this car (for reviews display)
            $feedbacks = \App\Models\Feedback::whereHas('booking', function($q) use ($car) {
                $q->where('car_id', $car->id)->where('status', 'completed');
            })->with('customer')->latest()->take(5)->get();
            
            return view('bookings.show', compact('car', 'customer', 'locations', 'feedbacks'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Car not found');
        }
    }

    public function create(Request $request)
    {
        $carName = $request->query('car');
        return view('bookings.create', ['carName' => $carName]);
    }

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
        
        // Calculate hours between pickup and dropoff
        $pickup = \Carbon\Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);
        $dropoff = \Carbon\Carbon::parse($validated['dropoff_date'] . ' ' . $validated['dropoff_time']);
        $hours = max(1, $pickup->diffInHours($dropoff));
        
        $basePrice = ($car->base_rate_per_hour ?? 0) * $hours;
        $promoDiscount = 0.00;
        $voucherDiscount = 0.00;
        $totalRentalAmount = $basePrice - $promoDiscount - $voucherDiscount;
        $depositAmount = 50.00; // Default deposit RM50
        $finalAmount = $totalRentalAmount;

        // Get location IDs (create if they don't exist)
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