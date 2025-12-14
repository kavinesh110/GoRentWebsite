<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'home');

Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');