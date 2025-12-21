<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/cars/{id}', [App\Http\Controllers\BookingController::class, 'show'])->name('cars.show');
Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');