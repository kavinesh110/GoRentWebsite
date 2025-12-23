<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/cars/{id}', [App\Http\Controllers\BookingController::class, 'show'])->name('cars.show');
Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');

// Customer authentication routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showCustomerLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showCustomerRegister'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::get('/register/success', [App\Http\Controllers\AuthController::class, 'showRegisterSuccess'])->name('register.success');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Staff authentication routes (separate from customer login)
Route::get('/staff/login', [App\Http\Controllers\AuthController::class, 'showStaffLogin'])->name('staff.login');
Route::post('/staff/login', [App\Http\Controllers\AuthController::class, 'login'])->name('staff.login.post');
Route::get('/staff/register', [App\Http\Controllers\AuthController::class, 'showStaffRegister'])->name('staff.register');

// Customer routes (protected by ensureCustomer middleware in controller)
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [App\Http\Controllers\CustomerController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [App\Http\Controllers\CustomerController::class, 'bookingsShow'])->name('bookings.show');
    Route::get('/booking/payment', [App\Http\Controllers\CustomerController::class, 'bookingsPayment'])->name('bookings.payment');
    Route::post('/booking/payment', [App\Http\Controllers\CustomerController::class, 'bookingsPaymentSubmit'])->name('bookings.payment.submit');
    Route::post('/bookings/{id}/cancel', [App\Http\Controllers\CustomerController::class, 'bookingsCancel'])->name('bookings.cancel');
    Route::get('/profile', [App\Http\Controllers\CustomerController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\CustomerController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/documents', [App\Http\Controllers\CustomerController::class, 'profileDocumentsUpdate'])->name('profile.documents.update');
    Route::get('/loyalty-rewards', [App\Http\Controllers\CustomerController::class, 'loyaltyRewards'])->name('loyalty-rewards');
    Route::post('/bookings/{id}/feedback', [App\Http\Controllers\CustomerController::class, 'bookingsSubmitFeedback'])->name('bookings.feedback.store');
    Route::post('/bookings/{id}/receipt', [App\Http\Controllers\CustomerController::class, 'bookingsUploadReceipt'])->name('bookings.receipt.upload');
});

// Staff routes (protected by ensureStaff middleware in controller)
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [App\Http\Controllers\StaffController::class, 'dashboard'])->name('dashboard');
    
    // Car management
    Route::get('/cars', [App\Http\Controllers\StaffController::class, 'cars'])->name('cars');
    Route::get('/cars/create', [App\Http\Controllers\StaffController::class, 'carsCreate'])->name('cars.create');
    Route::post('/cars', [App\Http\Controllers\StaffController::class, 'carsStore'])->name('cars.store');
    Route::get('/cars/{id}/edit', [App\Http\Controllers\StaffController::class, 'carsEdit'])->name('cars.edit');
    Route::put('/cars/{id}', [App\Http\Controllers\StaffController::class, 'carsUpdate'])->name('cars.update');
    
    // Booking management
    Route::get('/bookings', [App\Http\Controllers\StaffController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [App\Http\Controllers\StaffController::class, 'bookingsShow'])->name('bookings.show');
    Route::patch('/bookings/{id}/status', [App\Http\Controllers\StaffController::class, 'bookingsUpdateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/penalties', [App\Http\Controllers\StaffController::class, 'bookingsAddPenalty'])->name('bookings.penalties.store');
    Route::post('/bookings/{id}/inspections', [App\Http\Controllers\StaffController::class, 'bookingsAddInspection'])->name('bookings.inspections.store');
    
    // Payment management
    Route::post('/bookings/{id}/payments', [App\Http\Controllers\StaffController::class, 'paymentsStore'])->name('payments.store');
    Route::patch('/payments/{id}/verify', [App\Http\Controllers\StaffController::class, 'paymentsVerify'])->name('payments.verify');
    
    // Customer management
    Route::get('/customers', [App\Http\Controllers\StaffController::class, 'customers'])->name('customers');
    Route::get('/customers/{id}', [App\Http\Controllers\StaffController::class, 'customersShow'])->name('customers.show');
    Route::patch('/customers/{id}/verify', [App\Http\Controllers\StaffController::class, 'customersVerify'])->name('customers.verify');
    Route::patch('/customers/{id}/blacklist', [App\Http\Controllers\StaffController::class, 'customersBlacklist'])->name('customers.blacklist');
    
    // Activity management
    Route::get('/activities', [App\Http\Controllers\StaffController::class, 'activities'])->name('activities');
    Route::get('/activities/create', [App\Http\Controllers\StaffController::class, 'activitiesCreate'])->name('activities.create');
    Route::post('/activities', [App\Http\Controllers\StaffController::class, 'activitiesStore'])->name('activities.store');
    Route::get('/activities/{id}/edit', [App\Http\Controllers\StaffController::class, 'activitiesEdit'])->name('activities.edit');
    Route::put('/activities/{id}', [App\Http\Controllers\StaffController::class, 'activitiesUpdate'])->name('activities.update');
    Route::delete('/activities/{id}', [App\Http\Controllers\StaffController::class, 'activitiesDestroy'])->name('activities.destroy');
});
