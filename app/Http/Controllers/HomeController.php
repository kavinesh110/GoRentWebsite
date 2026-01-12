<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Activity;
use App\Models\CarLocation;
use App\Models\Booking;
use App\Models\SupportTicket;
use App\Models\Customer;
use App\Models\Staff;
use App\Mail\SupportTicketNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Handles the homepage/public facing views
 */
class HomeController extends Controller
{
    /**
     * Display the homepage
     * Shows all available cars and active promotional activities
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get only available cars (exclude cars in use or maintenance)
        // This ensures customers only see cars they can actually book
        $cars = Car::where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active promotions only (not company schedules, not expired)
        // Only show promotions (type='promotion') with end_date >= today
        $activities = Activity::where('type', 'promotion')
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();
        
        // Get available locations from database (for location dropdown in search bar)
        // Get all locations that can be used for pickup/dropoff
        $locations = CarLocation::whereIn('type', ['pickup', 'dropoff', 'both'])
            ->orderBy('name')
            ->get();
        
        // If no locations exist, create "Student Mall" as default
        if ($locations->isEmpty()) {
            CarLocation::create([
                'name' => 'Student Mall',
                'type' => 'both',
            ]);
            $locations = CarLocation::where('name', 'Student Mall')->get();
        }
        
        // Check if logged-in customer has completed bookings (for support form access)
        $hasCompletedBooking = false;
        $customerEmail = null;
        $completedBookings = collect();
        if (session('auth_role') === 'customer' && session('auth_id')) {
            $customerId = session('auth_id');
            $completedBookings = Booking::with('car')
                ->where('customer_id', $customerId)
                ->where('status', 'completed')
                ->orderBy('end_datetime', 'desc')
                ->get();
            $hasCompletedBooking = $completedBookings->isNotEmpty();
            $customer = Customer::find($customerId);
            $customerEmail = $customer->email ?? null;
        }
        
        return view('home', compact('cars', 'activities', 'locations', 'hasCompletedBooking', 'customerEmail', 'completedBookings'));
    }
    
    /**
     * Handle support ticket submission from homepage
     * Only allows submission if customer has at least one completed booking
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function supportSubmit(Request $request)
    {
        // Check if user is logged in as customer
        if (session('auth_role') !== 'customer' || !session('auth_id')) {
            return redirect()->route('home')->with('error', 'Please login as a customer to submit a support request.');
        }
        
        $customerId = session('auth_id');
        
        // Verify customer has at least one completed booking
        $hasCompletedBooking = Booking::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->exists();
            
        if (!$hasCompletedBooking) {
            return redirect()->route('home')->with('error', 'Support tickets can only be submitted after you have completed at least one booking with us.');
        }
        
        // Get customer data
        $customer = Customer::findOrFail($customerId);
        
        // Validate form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'booking_id' => 'required|exists:bookings,booking_id',
            'category' => 'required|in:cleanliness,lacking_facility,bluetooth,engine,others',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        // Verify the booking belongs to this customer
        $booking = Booking::where('booking_id', $validated['booking_id'])
            ->where('customer_id', $customerId)
            ->firstOrFail();
        
        // Create support ticket
        $ticket = SupportTicket::create([
            'customer_id' => $customerId,
            'booking_id' => $booking->booking_id,
            'car_id' => $booking->car_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $customer->phone,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);
        
        // Send email notification to all staff members
        try {
            // Load the car relationship for the email
            $ticket->load('car');
            
            // Get all staff email addresses
            $staffEmails = Staff::pluck('email')->filter()->toArray();
            
            if (!empty($staffEmails)) {
                Mail::to($staffEmails)->send(new SupportTicketNotification($ticket));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request - ticket was created successfully
            Log::error('Failed to send support ticket notification email: ' . $e->getMessage());
        }
        
        return redirect()->to(route('home') . '#support')->with('success', 'Your support request has been submitted successfully. We will get back to you soon!');
    }

    /**
     * Display all available cars with filtering
     * Supports filtering by location, dates, and car type
     * 
     * @param Request $request Contains filter parameters (location, start_date, end_date, type)
     * @return \Illuminate\View\View
     */
    public function cars(Request $request)
    {
        // Start with available cars only
        $query = Car::where('status', 'available');

        // Filter by car type
        if ($request->has('type') && $request->type !== '') {
            $query->where('car_type', strtolower($request->type));
        }

        // Filter by date availability - exclude cars that are booked during the requested period
        if ($request->has('start_date') && $request->has('end_date') && 
            $request->start_date !== '' && $request->end_date !== '') {
            
            // Parse dates and set default times (start of day for pickup, end of day for dropoff)
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            
            // Ensure end date is after start date
            if ($endDate->lt($startDate)) {
                $endDate = $startDate->copy()->endOfDay();
            }
            
            // Exclude cars that have overlapping bookings
            // A booking overlaps if it has any time overlap with the requested period
            // This matches the logic used in BookingController@store
            $query->whereDoesntHave('bookings', function($q) use ($startDate, $endDate) {
                $q->whereIn('status', ['created', 'verified', 'confirmed', 'active'])
                  ->where(function($query) use ($startDate, $endDate) {
                      // Booking starts during requested period
                      $query->whereBetween('start_datetime', [$startDate, $endDate])
                            // OR booking ends during requested period
                            ->orWhereBetween('end_datetime', [$startDate, $endDate])
                            // OR booking completely encompasses the requested period
                            ->orWhere(function($q) use ($startDate, $endDate) {
                                $q->where('start_datetime', '<=', $startDate)
                                  ->where('end_datetime', '>=', $endDate);
                            });
                  });
            });
        }

        // Order and paginate results (12 cars per page) and preserve query string
        $cars = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Get locations for the filter bar
        $locations = CarLocation::whereIn('type', ['pickup', 'dropoff', 'both'])
            ->orderBy('name')
            ->get();
        
        if ($locations->isEmpty()) {
            CarLocation::create([
                'name' => 'Student Mall',
                'type' => 'both',
            ]);
            $locations = CarLocation::where('name', 'Student Mall')->get();
        }

        // Pass filter values to view for maintaining form state
        $filters = [
            'location' => $request->get('location', ''),
            'start_date' => $request->get('start_date', date('Y-m-d')),
            'end_date' => $request->get('end_date', date('Y-m-d', strtotime('+1 day'))),
            'type' => $request->get('type', ''),
        ];

        return view('cars.index', compact('cars', 'locations', 'filters'));
    }
}