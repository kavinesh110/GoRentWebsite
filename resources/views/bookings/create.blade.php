@extends('layouts.app')

@section('title', 'Book Car - Hasta GoRent')

@section('content')
<div class="container-custom" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-soft" style="border-radius: 16px; border: none; overflow: hidden;">
                <div class="card-header bg-white" style="padding: 24px; border-bottom: 1px solid #f0f0f0;">
                    <h2 style="font-size: 24px; font-weight: 700; margin: 0;">Book {{ $carName ?? 'Car' }}</h2>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="car_name" class="form-label" style="font-weight: 600;">Selected Vehicle</label>
                            <input type="text" class="form-control" id="car_name" name="car_name" value="{{ $carName }}" readonly style="background-color: #f8f9fa;">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="pickup_date" class="form-label" style="font-weight: 600;">Pickup Date</label>
                                <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="pickup_time" class="form-label" style="font-weight: 600;">Pickup Time</label>
                                <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="return_date" class="form-label" style="font-weight: 600;">Return Date</label>
                                <input type="date" class="form-control" id="return_date" name="return_date" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="return_time" class="form-label" style="font-weight: 600;">Return Time</label>
                                <input type="time" class="form-control" id="return_time" name="return_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                             <label for="location" class="form-label" style="font-weight: 600;">Location</label>
                             <input type="text" class="form-control" id="location" name="location" placeholder="Johor Bahru, Malaysia" value="Johor Bahru, Malaysia">
                        </div>

                        <div class="mb-4">
                            <label for="customer_name" class="form-label" style="font-weight: 600;">Full Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Your Name" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="customer_email" class="form-label" style="font-weight: 600;">Email Address</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="name@example.com" required>
                        </div>

                        <button type="submit" class="btn btn-hasta w-100" style="padding: 12px; font-weight: 700; font-size: 16px;">Proceed to Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
