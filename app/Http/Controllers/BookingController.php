<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;

class BookingController extends Controller
{
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('bookings.show', compact('car'));
    }

    public function create(Request $request)
    {
        $carName = $request->query('car');
        return view('bookings.create', ['carName' => $carName]);
    }
}