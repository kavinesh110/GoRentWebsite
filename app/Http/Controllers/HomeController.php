<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Activity;

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
        // Get all cars from database
        // TODO: Consider filtering by status='available' to only show available cars
        $cars = Car::all();

        // Get active activities/promotions (not expired)
        // Activities with end_date >= today are considered active
        $activities = Activity::where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('home', compact('cars', 'activities'));
    }
}