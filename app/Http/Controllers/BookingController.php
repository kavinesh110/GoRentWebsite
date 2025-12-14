<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $carName = $request->query('car');
        return view('bookings.create', ['carName' => $carName]);
    }
}
