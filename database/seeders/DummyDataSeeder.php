<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Car;
use App\Models\Booking;
use App\Models\CarLocation;
use App\Models\MaintenanceRecord;
use App\Models\CancellationRequest;
use App\Models\SupportTicket;
use App\Models\Payment;
use App\Models\Inspection;
use App\Models\Penalty;
use App\Models\Feedback;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\Staff;
use App\Models\RentalPhoto;

/**
 * DummyDataSeeder
 * 
 * Creates comprehensive dummy data for testing and demonstration:
 * - 30 customers with mixed verification statuses and loyalty stamps
 * - 30 bookings with mixed statuses (past, present, future dates)
 * - At least 3 maintenance records per car
 * - At least 3 cancellation requests with mixed statuses
 * - About 5 support tickets with mixed statuses
 * - Payments, inspections, penalties, feedbacks, voucher redemptions
 * 
 * IMPORTANT NOTES:
 * 1. This seeder ONLY uses existing cars in the database - it does NOT create new cars
 * 2. Ensure you have cars in your database (run CarSeeder if needed: php artisan db:seed --class=CarSeeder)
 * 3. Ensure you have at least one Staff member in the database
 * 4. Update placeholder image paths in this seeder with actual image paths
 * 5. The seeder ensures no booking date conflicts (same car can't be booked at same time)
 * 
 * Usage:
 * php artisan db:seed --class=DummyDataSeeder
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing data
        $cars = Car::all();
        $locations = CarLocation::all();
        $staff = Staff::first();
        $vouchers = Voucher::where('is_active', true)->get();

        if ($cars->isEmpty()) {
            $this->command->error('No cars found. Please run CarSeeder first.');
            return;
        }

        if ($locations->isEmpty()) {
            // Create default location
            $location = CarLocation::create([
                'name' => 'Student Mall',
                'type' => 'both',
            ]);
            $locations = collect([$location]);
        }

        // Kolej names
        $kolejNames = [
            'Kolej Tun Hussein Onn (KTHO)',
            'Kolej Tun Dr. Ismail (KTDI)',
            'Kolej Tuanku Canselor (KTC)',
            'Kolej Perdana (KP)',
            'Kolej 9 & 10',
            'Kolej Datin Seri Endon (KDSE)',
            'Kolej Dato\' Onn Jaafar (KDOJ)',
            'Scholar\'s Inn',
        ];

        // Placeholder image paths (update these with actual images later)
        $placeholderImage = 'images/placeholders/document.jpg';

        // Create 30 customers with mixed verification statuses
        $this->command->info('Creating customers...');
        $customers = [];
        for ($i = 1; $i <= 30; $i++) {
            $email = "customer$i@student.utm.my";
            
            // Only create if customer doesn't exist (check by email)
            $customer = Customer::firstOrCreate(
                ['email' => $email],
                [
                    'full_name' => "Customer $i",
                    'email' => $email,
                    'phone' => '01' . str_pad(rand(20000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'password_hash' => Hash::make('password123'),
                    'ic_url' => $placeholderImage,
                    'utmid_url' => $placeholderImage,
                    'license_url' => $placeholderImage,
                    'utm_role' => rand(0, 1) ? 'student' : 'staff',
                    'college_name' => $kolejNames[array_rand($kolejNames)],
                    'verification_status' => 'pending',
                    'deposit_balance' => 0,
                    'total_rental_hours' => 0,
                    'total_stamps' => 0,
                ]
            );
            
            // Only update if customer was just created (don't overwrite existing data)
            if ($customer->wasRecentlyCreated) {
                $verificationStatus = ['pending', 'approved', 'approved', 'rejected'][rand(0, 3)];
                $isBlacklisted = ($i % 10 === 0);
                $totalStamps = rand(0, 45);
                $totalHours = $totalStamps * 9;
                
                $customer->update([
                    'verification_status' => $verificationStatus,
                    'verified_by' => ($verificationStatus === 'approved' && $staff) ? $staff->staff_id : null,
                    'verified_at' => ($verificationStatus === 'approved') ? Carbon::now()->subDays(rand(1, 90)) : null,
                    'is_blacklisted' => $isBlacklisted,
                    'blacklist_reason' => $isBlacklisted ? 'Violation of rental terms' : null,
                    'blacklist_since' => $isBlacklisted ? Carbon::now()->subDays(rand(1, 30)) : null,
                    'deposit_balance' => rand(0, 200) * 10,
                    'total_rental_hours' => $totalHours,
                    'total_stamps' => $totalStamps,
                ]);
            }
            
            $customers[] = $customer;
        }

        // Create 30 bookings with mixed statuses and dates (ensuring no conflicts)
        $this->command->info('Creating bookings...');
        $bookings = [];
        $bookingDates = []; // Track dates to avoid conflicts

        $statuses = ['created', 'verified', 'confirmed', 'active', 'completed', 'cancelled'];
        $statusWeights = [
            'created' => 3,
            'verified' => 3,
            'confirmed' => 3,
            'active' => 2,
            'completed' => 10,
            'cancelled' => 2,
        ];

        for ($i = 0; $i < 30; $i++) {
            $car = $cars->random();
            $customer = $customers[array_rand($customers)];
            $pickupLocation = $locations->random();
            $dropoffLocation = $locations->random();

            // Generate non-conflicting dates
            $attempts = 0;
            do {
                $daysOffset = rand(-60, 30); // Past 60 days to future 30 days
                $pickupDate = Carbon::now()->addDays($daysOffset);
                $rentalHours = rand(2, 72); // 2-72 hours
                $dropoffDate = $pickupDate->copy()->addHours($rentalHours);
                $attempts++;
            } while ($this->hasConflict($car->id, $pickupDate, $dropoffDate, $bookingDates) && $attempts < 50);

            if ($attempts >= 50) {
                continue; // Skip if can't find non-conflicting date
            }

            // Record this booking's dates
            $bookingDates[] = [
                'car_id' => $car->id,
                'start' => $pickupDate,
                'end' => $dropoffDate,
            ];

            // Determine status based on date - past bookings must be completed or cancelled
            $status = 'created';
            if ($dropoffDate->isPast()) {
                // Past bookings can only be completed or cancelled
                $status = rand(0, 1) ? 'completed' : 'cancelled';
            } elseif ($pickupDate->isPast() && $dropoffDate->isFuture()) {
                // Currently active booking
                $status = 'active';
            } elseif ($pickupDate->isFuture()) {
                // Future bookings can be in various stages
                $status = ['created', 'verified', 'confirmed'][rand(0, 2)];
            }

            $basePrice = $car->base_rate_per_hour * $rentalHours;
            $voucherDiscount = 0;
            $voucherId = null;

            // Randomly apply voucher
            if (rand(0, 1) && $vouchers->isNotEmpty() && $customer->total_stamps >= 9) {
                $voucher = $vouchers->random();
                if ($voucher->discount_type === 'percent') {
                    $voucherDiscount = ($basePrice * $voucher->discount_value) / 100;
                } else {
                    $voucherDiscount = min($voucher->discount_value, $basePrice);
                }
                $voucherId = $voucher->voucher_id;
            }

            $totalRentalAmount = $basePrice - $voucherDiscount;
            $depositAmount = 50.00;
            $finalAmount = $totalRentalAmount;

            $booking = Booking::create([
                'customer_id' => $customer->customer_id,
                'car_id' => $car->id,
                'pickup_location_id' => $pickupLocation->location_id,
                'dropoff_location_id' => $dropoffLocation->location_id,
                'start_datetime' => $pickupDate,
                'end_datetime' => $dropoffDate,
                'rental_hours' => $rentalHours,
                'base_price' => $basePrice,
                'promo_discount' => 0.00,
                'voucher_discount' => $voucherDiscount,
                'total_rental_amount' => $totalRentalAmount,
                'deposit_amount' => $depositAmount,
                'deposit_used_amount' => 0.00,
                'deposit_refund_amount' => 0.00,
                'final_amount' => $finalAmount,
                'status' => $status,
                'agreement_signed_at' => in_array($status, ['active', 'completed']) ? $pickupDate->copy()->subHours(rand(1, 24)) : null,
            ]);

            // Create voucher redemption if voucher was used
            if ($voucherId) {
                VoucherRedemption::create([
                    'voucher_id' => $voucherId,
                    'customer_id' => $customer->customer_id,
                    'booking_id' => $booking->booking_id,
                    'discount_amount' => $voucherDiscount,
                    'redeemed_at' => $booking->created_at,
                ]);
            }

            $bookings[] = $booking;
        }

        // Create payments for bookings
        $this->command->info('Creating payments...');
        foreach ($bookings as $booking) {
            if (in_array($booking->status, ['verified', 'confirmed', 'active', 'completed'])) {
                // For completed bookings, payment must be verified
                $paymentStatus = ($booking->status === 'completed') ? 'verified' : (['pending', 'verified'][rand(0, 1)]);
                
                Payment::create([
                    'booking_id' => $booking->booking_id,
                    'penalty_id' => null,
                    'amount' => $booking->final_amount + $booking->deposit_amount,
                    'payment_type' => 'full_payment',
                    'payment_method' => ['bank_transfer', 'e-wallet', 'cash'][rand(0, 2)],
                    'bank_name' => ['Maybank', 'CIMB', 'Public Bank', 'RHB'][rand(0, 3)],
                    'account_holder_name' => $booking->customer->full_name,
                    'account_number' => str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                    'receipt_url' => $placeholderImage,
                    'payment_date' => $booking->created_at->copy()->addHours(rand(1, 24)),
                    'status' => $paymentStatus,
                ]);
            }
        }

        // Create inspections for confirmed/active/completed bookings
        $this->command->info('Creating inspections...');
        foreach ($bookings as $booking) {
            if (in_array($booking->status, ['confirmed', 'active', 'completed'])) {
                // Before pickup inspection
                Inspection::create([
                    'booking_id' => $booking->booking_id,
                    'car_id' => $booking->car_id,
                    'inspection_type' => 'pickup',
                    'type' => 'before',
                    'datetime' => $booking->start_datetime->copy()->subHours(rand(1, 6)),
                    'odometer_reading' => $booking->car->current_mileage - rand(100, 1000),
                    'mileage_reading' => $booking->car->current_mileage - rand(100, 1000),
                    'fuel_level' => rand(1, 8), // 0-8 bars
                    'notes' => 'Vehicle in good condition',
                    'photos' => json_encode([$placeholderImage, $placeholderImage]),
                    'status' => 'completed',
                    'inspected_by' => $staff ? $staff->staff_id : null,
                    'inspected_at' => $booking->start_datetime->copy()->subHours(rand(1, 6)),
                ]);

                // After return inspection (for completed bookings)
                if ($booking->status === 'completed') {
                    Inspection::create([
                        'booking_id' => $booking->booking_id,
                        'car_id' => $booking->car_id,
                        'inspection_type' => 'return',
                        'type' => 'after',
                        'datetime' => $booking->end_datetime->copy()->addHours(rand(1, 6)),
                        'odometer_reading' => $booking->car->current_mileage + rand(50, 500),
                        'mileage_reading' => $booking->car->current_mileage + rand(50, 500),
                        'fuel_level' => rand(1, 8), // 0-8 bars
                        'notes' => 'Vehicle returned in acceptable condition',
                        'photos' => json_encode([$placeholderImage, $placeholderImage]),
                        'status' => 'completed',
                        'inspected_by' => $staff ? $staff->staff_id : null,
                        'inspected_at' => $booking->end_datetime->copy()->addHours(rand(1, 6)),
                    ]);
                }
            }
        }

        // Create rental photos for active/completed bookings
        $this->command->info('Creating rental photos...');
        foreach ($bookings as $booking) {
            if (in_array($booking->status, ['active', 'completed'])) {
                // Agreement photo (required for active/completed bookings)
                RentalPhoto::create([
                    'booking_id' => $booking->booking_id,
                    'uploaded_by_user_id' => $booking->customer_id,
                    'uploaded_by_role' => 'customer',
                    'photo_type' => 'agreement',
                    'photo_url' => $placeholderImage,
                    'taken_at' => $booking->agreement_signed_at ?? $booking->start_datetime->copy()->subHours(rand(1, 6)),
                ]);

                // Pickup photo (required for active/completed bookings)
                RentalPhoto::create([
                    'booking_id' => $booking->booking_id,
                    'uploaded_by_user_id' => $booking->customer_id,
                    'uploaded_by_role' => 'customer',
                    'photo_type' => 'pickup',
                    'photo_url' => $placeholderImage,
                    'taken_at' => $booking->start_datetime->copy()->addMinutes(rand(5, 30)),
                ]);
            }
        }

        // Create penalties for some completed bookings
        $this->command->info('Creating penalties...');
        $penaltyTypes = ['late', 'fuel', 'damage', 'other'];
        foreach ($bookings as $booking) {
            if ($booking->status === 'completed' && rand(0, 2) === 0) { // 33% chance
                $penalty = Penalty::create([
                    'booking_id' => $booking->booking_id,
                    'penalty_type' => $penaltyTypes[array_rand($penaltyTypes)],
                    'description' => 'Penalty for ' . $penaltyTypes[array_rand($penaltyTypes)] . ' issue',
                    'amount' => rand(20, 200),
                    'is_installment' => rand(0, 1),
                    'status' => ['pending', 'partially_paid', 'settled'][rand(0, 2)],
                ]);

                // Create payment for settled penalties
                if ($penalty->status === 'settled') {
                    Payment::create([
                        'booking_id' => $booking->booking_id,
                        'penalty_id' => $penalty->penalty_id,
                        'amount' => $penalty->amount,
                        'payment_type' => 'penalty',
                        'payment_method' => ['bank_transfer', 'e-wallet'][rand(0, 1)],
                        'bank_name' => ['Maybank', 'CIMB'][rand(0, 1)],
                        'account_holder_name' => $booking->customer->full_name,
                        'account_number' => str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                        'receipt_url' => $placeholderImage,
                        'payment_date' => $booking->end_datetime->copy()->addDays(rand(1, 7)),
                        'status' => 'verified',
                    ]);
                }
            }
        }

        // Update completed bookings to have deposit returned
        $this->command->info('Updating completed bookings with deposit return info...');
        foreach ($bookings as $booking) {
            if ($booking->status === 'completed') {
                // Set deposit decision and refund amount for completed bookings
                $depositDecision = ['refund', 'carry_forward'][rand(0, 1)];
                $depositRefundAmount = ($depositDecision === 'refund') ? $booking->deposit_amount : 0.00;
                
                $booking->update([
                    'deposit_decision' => $depositDecision,
                    'deposit_refund_amount' => $depositRefundAmount,
                ]);
            }
        }

        // Create feedbacks for completed bookings (only if feedback doesn't exist)
        $this->command->info('Creating feedbacks...');
        foreach ($bookings as $booking) {
            if ($booking->status === 'completed' && rand(0, 1)) { // 50% chance
                Feedback::firstOrCreate(
                    ['booking_id' => $booking->booking_id],
                    [
                        'customer_id' => $booking->customer_id,
                        'rating' => rand(3, 5), // 3-5 stars
                        'comment' => 'Great service and clean vehicle!',
                        'reported_issue' => rand(0, 1),
                    ]
                );
            }
        }

        // Create maintenance records (3 per car) - only if they don't already have 3+
        $this->command->info('Creating maintenance records...');
        foreach ($cars as $car) {
            $existingCount = MaintenanceRecord::where('car_id', $car->id)->count();
            if ($existingCount >= 3) {
                continue; // Skip if car already has 3+ maintenance records
            }
            
            $currentMileage = $car->current_mileage;
            $recordsToCreate = 3 - $existingCount;
            for ($i = 0; $i < $recordsToCreate; $i++) {
                $serviceMileage = $currentMileage - (($i + 1) * rand(5000, 10000));
                MaintenanceRecord::create([
                    'car_id' => $car->id,
                    'service_date' => Carbon::now()->subMonths(rand(1, 12) + ($i * 3)),
                    'description' => ['Regular service', 'Oil change', 'Tire replacement', 'Brake inspection'][rand(0, 3)],
                    'mileage_at_service' => max(0, $serviceMileage),
                    'cost' => rand(100, 500),
                ]);
            }
        }

        // Create cancellation requests (at least 3)
        // Only for bookings that are not already cancelled
        // Cancellation status must be either 'refunded' or 'rejected' (no pending/approved)
        $this->command->info('Creating cancellation requests...');
        $eligibleBookings = collect($bookings)->where('status', '!=', 'cancelled')->take(5);
        $reasonTypes = ['change_of_plans', 'found_alternative', 'financial_reasons', 'emergency', 'other'];
        
        // Cancellation can only be refunded or rejected (no pending/approved)
        $cancellationStatuses = ['refunded', 'rejected'];

        foreach ($eligibleBookings as $booking) {
            $status = $cancellationStatuses[array_rand($cancellationStatuses)];
            $processedAt = Carbon::now()->subDays(rand(1, 30));
            
            CancellationRequest::create([
                'booking_id' => $booking->booking_id,
                'customer_id' => $booking->customer_id,
                'reason_type' => $reasonTypes[array_rand($reasonTypes)],
                'reason_details' => 'Need to cancel due to unexpected circumstances',
                'bank_name' => ['Maybank', 'CIMB', 'Public Bank'][rand(0, 2)],
                'bank_account_number' => str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                'bank_account_holder' => $booking->customer->full_name,
                'proof_document_url' => $placeholderImage,
                'status' => $status,
                'processed_by_staff_id' => $staff ? $staff->staff_id : null,
                'processed_at' => $processedAt,
                'staff_notes' => $status === 'refunded' ? 'Cancellation approved and refund processed' : 'Cancellation request rejected',
                'refund_amount' => ($status === 'refunded') ? $booking->deposit_amount : null,
                'refund_reference' => ($status === 'refunded') ? 'REF' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT) : null,
                'refunded_at' => ($status === 'refunded') ? $processedAt->copy()->addDays(rand(1, 3)) : null,
            ]);
        }

        // Create support tickets (about 5)
        $this->command->info('Creating support tickets...');
        $categories = ['cleanliness', 'lacking_facility', 'bluetooth', 'engine', 'others'];
        $ticketStatuses = ['open', 'in_progress', 'resolved', 'closed'];

        for ($i = 0; $i < 5; $i++) {
            $booking = $bookings[array_rand($bookings)];
            $customer = $customers[array_rand($customers)];
            $status = $ticketStatuses[array_rand($ticketStatuses)];

            SupportTicket::create([
                'customer_id' => $customer->customer_id,
                'booking_id' => (rand(0, 1)) ? $booking->booking_id : null,
                'car_id' => (rand(0, 1)) ? $booking->car_id : null,
                'maintenance_record_id' => null,
                'flagged_for_maintenance' => false,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'category' => $categories[array_rand($categories)],
                'subject' => 'Support Request ' . ($i + 1),
                'description' => 'I need assistance with my booking/vehicle',
                'status' => $status,
                'staff_response' => ($status !== 'open') ? 'Issue has been addressed' : null,
                'assigned_to' => ($status !== 'open' && $staff) ? $staff->staff_id : null,
                'resolved_at' => ($status === 'resolved') ? Carbon::now()->subDays(rand(1, 14)) : null,
                'closed_at' => ($status === 'closed') ? Carbon::now()->subDays(rand(1, 7)) : null,
            ]);
        }

        $this->command->info('Dummy data created successfully!');
        $this->command->info('- ' . count($customers) . ' customers');
        $this->command->info('- ' . count($bookings) . ' bookings');
        $this->command->info('- ' . MaintenanceRecord::count() . ' maintenance records');
        $this->command->info('- ' . CancellationRequest::count() . ' cancellation requests');
        $this->command->info('- ' . SupportTicket::count() . ' support tickets');
    }

    private function hasConflict($carId, $start, $end, $existingDates): bool
    {
        foreach ($existingDates as $date) {
            if ($date['car_id'] === $carId) {
                // Check if dates overlap
                if (($start >= $date['start'] && $start < $date['end']) ||
                    ($end > $date['start'] && $end <= $date['end']) ||
                    ($start <= $date['start'] && $end >= $date['end'])) {
                    return true;
                }
            }
        }
        return false;
    }
}
