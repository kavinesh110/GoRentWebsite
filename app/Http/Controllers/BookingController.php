<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;
use App\Models\CarLocation;

class BookingController extends Controller
{
    public function show($id)
    {
        try {
            $car = Car::findOrFail($id);
            
            // Check if car has required fields
            if (!$car->brand || !$car->model) {
                abort(404, 'Car information is incomplete');
            }
            
            return view('bookings.show', compact('car'));
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

        $booking = Booking::create([
            'customer_id' => 1, // TODO: Get from authenticated user
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

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking created successfully!');
    }
}