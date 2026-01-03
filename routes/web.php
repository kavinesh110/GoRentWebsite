<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/cars', [App\Http\Controllers\HomeController::class, 'cars'])->name('cars.index');

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
    Route::get('/bookings/{id}/cancel', [App\Http\Controllers\CustomerController::class, 'bookingsCancelForm'])->name('bookings.cancel.form');
    Route::post('/bookings/{id}/cancel', [App\Http\Controllers\CustomerController::class, 'bookingsCancelSubmit'])->name('bookings.cancel.submit');
    Route::get('/profile', [App\Http\Controllers\CustomerController::class, 'profile'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\CustomerController::class, 'profileUpdate'])->name('profile.update');
    Route::post('/profile/documents', [App\Http\Controllers\CustomerController::class, 'profileDocumentsUpdate'])->name('profile.documents.update');
    Route::get('/loyalty-rewards', [App\Http\Controllers\CustomerController::class, 'loyaltyRewards'])->name('loyalty-rewards');
    Route::post('/bookings/{id}/feedback', [App\Http\Controllers\CustomerController::class, 'bookingsSubmitFeedback'])->name('bookings.feedback.store');
    Route::post('/bookings/{id}/receipt', [App\Http\Controllers\CustomerController::class, 'bookingsUploadReceipt'])->name('bookings.receipt.upload');
    Route::post('/bookings/{id}/photos', [App\Http\Controllers\CustomerController::class, 'bookingsUploadPhoto'])->name('bookings.photos.upload');
    Route::post('/bookings/{id}/sign-agreement', [App\Http\Controllers\CustomerController::class, 'bookingsSignAgreement'])->name('bookings.sign-agreement');
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
    Route::delete('/cars/{id}', [App\Http\Controllers\StaffController::class, 'carsDestroy'])->name('cars.destroy');
    
    // Booking management
    Route::get('/bookings', [App\Http\Controllers\StaffController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{id}', [App\Http\Controllers\StaffController::class, 'bookingsShow'])->name('bookings.show');
    Route::patch('/bookings/{id}/status', [App\Http\Controllers\StaffController::class, 'bookingsUpdateStatus'])->name('bookings.update-status');
    Route::post('/bookings/{id}/penalties', [App\Http\Controllers\StaffController::class, 'bookingsAddPenalty'])->name('bookings.penalties.store');
    Route::post('/bookings/{id}/inspections', [App\Http\Controllers\StaffController::class, 'bookingsAddInspection'])->name('bookings.inspections.store');
    
    // Cancellation request management
    Route::get('/cancellation-requests', [App\Http\Controllers\StaffController::class, 'cancellationRequests'])->name('cancellation-requests');
    Route::get('/cancellation-requests/{id}', [App\Http\Controllers\StaffController::class, 'cancellationRequestsShow'])->name('cancellation-requests.show');
    Route::patch('/cancellation-requests/{id}/process', [App\Http\Controllers\StaffController::class, 'cancellationRequestsProcess'])->name('cancellation-requests.process');
    
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
    
    // Voucher management
    Route::get('/vouchers', [App\Http\Controllers\StaffController::class, 'vouchers'])->name('vouchers');
    Route::get('/vouchers/create', [App\Http\Controllers\StaffController::class, 'vouchersCreate'])->name('vouchers.create');
    Route::post('/vouchers', [App\Http\Controllers\StaffController::class, 'vouchersStore'])->name('vouchers.store');
    Route::get('/vouchers/{id}/edit', [App\Http\Controllers\StaffController::class, 'vouchersEdit'])->name('vouchers.edit');
    Route::put('/vouchers/{id}', [App\Http\Controllers\StaffController::class, 'vouchersUpdate'])->name('vouchers.update');
    Route::delete('/vouchers/{id}', [App\Http\Controllers\StaffController::class, 'vouchersDestroy'])->name('vouchers.destroy');
    
    // Maintenance records management
    Route::get('/cars/{id}/maintenance', [App\Http\Controllers\StaffController::class, 'maintenanceRecords'])->name('maintenance.index');
    Route::get('/cars/{id}/maintenance/create', [App\Http\Controllers\StaffController::class, 'maintenanceCreate'])->name('maintenance.create');
    Route::post('/cars/{id}/maintenance', [App\Http\Controllers\StaffController::class, 'maintenanceStore'])->name('maintenance.store');
    Route::get('/cars/{carId}/maintenance/{recordId}/edit', [App\Http\Controllers\StaffController::class, 'maintenanceEdit'])->name('maintenance.edit');
    Route::put('/cars/{carId}/maintenance/{recordId}', [App\Http\Controllers\StaffController::class, 'maintenanceUpdate'])->name('maintenance.update');
    Route::delete('/cars/{carId}/maintenance/{recordId}', [App\Http\Controllers\StaffController::class, 'maintenanceDestroy'])->name('maintenance.destroy');
    
    // Feedback management
    Route::get('/feedbacks', [App\Http\Controllers\StaffController::class, 'feedbacks'])->name('feedbacks');
    
    // Car location management
    Route::get('/locations', [App\Http\Controllers\StaffController::class, 'locations'])->name('locations');
    Route::get('/locations/create', [App\Http\Controllers\StaffController::class, 'locationsCreate'])->name('locations.create');
    Route::post('/locations', [App\Http\Controllers\StaffController::class, 'locationsStore'])->name('locations.store');
    Route::get('/locations/{id}/edit', [App\Http\Controllers\StaffController::class, 'locationsEdit'])->name('locations.edit');
    Route::put('/locations/{id}', [App\Http\Controllers\StaffController::class, 'locationsUpdate'])->name('locations.update');
    Route::delete('/locations/{id}', [App\Http\Controllers\StaffController::class, 'locationsDestroy'])->name('locations.destroy');
    
    // Export functionality
    Route::get('/bookings/export', [App\Http\Controllers\StaffController::class, 'bookingsExport'])->name('bookings.export');
    Route::get('/customers/export', [App\Http\Controllers\StaffController::class, 'customersExport'])->name('customers.export');
    
    // Calendar
    Route::get('/calendar', [App\Http\Controllers\StaffController::class, 'calendar'])->name('calendar');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\StaffController::class, 'reports'])->name('reports');
    
    // Deposit refund
    Route::post('/bookings/{id}/process-refund', [App\Http\Controllers\StaffController::class, 'bookingsProcessRefund'])->name('bookings.process-refund');
    
    // Penalties
    Route::get('/penalties', [App\Http\Controllers\StaffController::class, 'penaltiesIndex'])->name('penalties');
});
