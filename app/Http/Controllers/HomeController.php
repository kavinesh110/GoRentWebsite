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
}