<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Booking;
use App\Models\CarLocation;
use App\Models\Customer;
use App\Models\Feedback;
use App\Models\Voucher;

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
            
            // Check if car is available for booking
            if ($car->status !== 'available') {
                return redirect()->route('home')
                    ->with('error', 'This car is currently not available for booking. Please select another car.');
            }
            
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
            
            // Get available vouchers for this customer (for voucher selection in booking)
            $availableVouchers = \App\Models\Voucher::where('is_active', true)
                ->where('expiry_date', '>=', now())
                ->where('min_stamps_required', '<=', $customer->total_stamps ?? 0)
                ->whereDoesntHave('redemptions', function($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                })
                ->get();

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
            
            return view('bookings.show', compact('car', 'customer', 'locations', 'feedbacks', 'availableVouchers'));
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
            'voucher_id' => 'nullable|exists:vouchers,voucher_id',
        ]);

        $car = Car::findOrFail($validated['car_id']);
        
        // Check if car is still available
        if ($car->status !== 'available') {
            return redirect()->route('home')
                ->with('error', 'This car is no longer available. Please select another car.');
        }
        
        // Calculate rental duration in hours
        $pickup = \Carbon\Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);
        $dropoff = \Carbon\Carbon::parse($validated['dropoff_date'] . ' ' . $validated['dropoff_time']);
        
        // Check for overlapping bookings (same car cannot be booked at the same time)
        $hasConflict = Booking::where('car_id', $car->id)
            ->whereIn('status', ['created', 'verified', 'confirmed', 'active'])
            ->where(function($query) use ($pickup, $dropoff) {
                $query->whereBetween('start_datetime', [$pickup, $dropoff])
                    ->orWhereBetween('end_datetime', [$pickup, $dropoff])
                    ->orWhere(function($q) use ($pickup, $dropoff) {
                        $q->where('start_datetime', '<=', $pickup)
                          ->where('end_datetime', '>=', $dropoff);
                    });
            })
            ->exists();
        
        if ($hasConflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This car is already booked for the selected dates. Please choose different dates.');
        }
        $hours = max(1, $pickup->diffInHours($dropoff)); // Minimum 1 hour rental
        
        // Calculate pricing breakdown
        $basePrice = ($car->base_rate_per_hour ?? 0) * $hours;
        $promoDiscount = 0.00; // TODO: Implement promo code system
        
        // Process voucher redemption if provided
        $voucherDiscount = 0.00;
        $voucherId = null;
        if (!empty($validated['voucher_id'])) {
            $customerId = $request->session()->get('auth_id');
            $customer = Customer::findOrFail($customerId);
            
            $voucher = Voucher::where('voucher_id', $validated['voucher_id'])
                ->where('is_active', true)
                ->where('expiry_date', '>=', now())
                ->where('min_stamps_required', '<=', $customer->total_stamps ?? 0)
                ->whereDoesntHave('redemptions', function($q) use ($customerId) {
                    $q->where('customer_id', $customerId);
                })
                ->first();
            
            if ($voucher) {
                // Calculate discount based on voucher type
                if ($voucher->discount_type === 'percent') {
                    $voucherDiscount = ($basePrice * $voucher->discount_value) / 100;
                } else {
                    // Fixed amount discount
                    $voucherDiscount = min($voucher->discount_value, $basePrice); // Can't discount more than base price
                }
                $voucherId = $voucher->voucher_id;
            }
        }
        
        $totalRentalAmount = $basePrice - $promoDiscount - $voucherDiscount;
        $depositAmount = 50.00; // Default deposit amount (RM50)
        $finalAmount = $totalRentalAmount;
        // Get authenticated customer ID
        $customerId = $request->session()->get('auth_id');
        if (!$customerId || $request->session()->get('auth_role') !== 'customer') {
            return redirect()->route('login')->with('error', 'Please login to make a booking.');
        }

        // Store booking details in session until deposit payment is submitted
        $request->session()->put('pending_booking', [
            'customer_id' => $customerId,
            'car_id' => $validated['car_id'],
            'pickup_location' => $validated['pickup_location'],
            'dropoff_location' => $validated['dropoff_location'],
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'dropoff_date' => $validated['dropoff_date'],
            'dropoff_time' => $validated['dropoff_time'],
            'hours' => $hours,
            'base_price' => $basePrice,
            'promo_discount' => $promoDiscount,
            'voucher_discount' => $voucherDiscount,
            'total_rental_amount' => $totalRentalAmount,
            'deposit_amount' => $depositAmount,
            'final_amount' => $finalAmount,
            'voucher_id' => $voucherId,
            'billing_name' => $validated['name'],
            'billing_phone' => $validated['phone'],
            'billing_address' => $validated['address'],
            'billing_city' => $validated['city'],
        ]);

        return redirect()->route('customer.bookings.payment')
            ->with('success', 'Booking details saved. Please complete the deposit payment to create your booking.');
    }
}