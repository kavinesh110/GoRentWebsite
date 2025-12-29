<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Activity;
use App\Models\CarLocation;

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
        
        return view('home', compact('cars', 'activities', 'locations'));
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