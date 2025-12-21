<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class HomeController extends Controller
{
    public function index()
    {
        // Get all cars (you can filter by status later if needed)
        $cars = Car::all();
        
        return view('home', compact('cars'));
    }
}