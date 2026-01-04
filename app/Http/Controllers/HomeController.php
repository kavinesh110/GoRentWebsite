<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Activity;
use App\Models\CarLocation;
use App\Models\Booking;
use App\Models\SupportTicket;
use App\Models\Customer;

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

        // Get active activities/promotions (not expired)
        // Activities with end_date >= today are considered active
        $activities = Activity::where('end_date', '>=', now())
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
        if (session('auth_role') === 'customer' && session('auth_id')) {
            $customerId = session('auth_id');
            $hasCompletedBooking = Booking::where('customer_id', $customerId)
                ->where('status', 'completed')
                ->exists();
            $customer = Customer::find($customerId);
            $customerEmail = $customer->email ?? null;
        }
        
        return view('home', compact('cars', 'activities', 'locations', 'hasCompletedBooking', 'customerEmail'));
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
            'category' => 'required|in:cleanliness,lacking_facility,bluetooth,engine,others',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);
        
        // Create support ticket
        SupportTicket::create([
            'customer_id' => $customerId,
            'booking_id' => null, // Can be linked later by staff if related to specific booking
            'car_id' => null, // Can be linked later by staff if related to specific car
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $customer->phone,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);
        
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

        // Filter by car type (based on model name patterns)
        // Since there's no explicit type field, we filter by common model patterns
        if ($request->has('type') && $request->type !== '') {
            $type = strtolower($request->type);
            $query->where(function($q) use ($type) {
                switch ($type) {
                    case 'hatchback':
                        $q->where('model', 'like', '%myvi%')
                          ->orWhere('model', 'like', '%axia%')
                          ->orWhere('model', 'like', '%bezza%')
                          ->orWhere('model', 'like', '%picanto%');
                        break;
                    case 'sedan':
                        $q->where('model', 'like', '%vios%')
                          ->orWhere('model', 'like', '%city%')
                          ->orWhere('model', 'like', '%almera%')
                          ->orWhere('model', 'like', '%accord%');
                        break;
                    case 'suv':
                        $q->where('model', 'like', '%x70%')
                          ->orWhere('model', 'like', '%aruz%')
                          ->orWhere('model', 'like', '%hr-v%')
                          ->orWhere('model', 'like', '%cr-v%')
                          ->orWhere('model', 'like', '%rush%');
                        break;
                    case 'van':
                        $q->where('model', 'like', '%vellfire%')
                          ->orWhere('model', 'like', '%alphard%')
                          ->orWhere('model', 'like', '%starex%')
                          ->orWhere('model', 'like', '%hiace%');
                        break;
                }
            });
        }

        // Order and paginate results (12 cars per page)
        $cars = $query->orderBy('created_at', 'desc')->paginate(12);

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