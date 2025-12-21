<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\Activity;

class HomeController extends Controller
{
    public function index()
    {
        // Get all cars (you can filter by status later if needed)
        $cars = Car::all();

        // Get active activities/promotions
        $activities = Activity::where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('home', compact('cars', 'activities'));
    }
}