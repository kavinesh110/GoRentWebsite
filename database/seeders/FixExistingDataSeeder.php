<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\CancellationRequest;

/**
 * FixExistingDataSeeder
 * 
 * Fixes existing data to match realistic business logic:
 * 1. Past bookings should only be "completed" or "cancelled"
 * 2. Cancellation requests should only be "refunded" or "rejected" (not "approved" or "pending")
 */
class FixExistingDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Fixing existing bookings...');
        
        // Fix past bookings - they should only be "completed" or "cancelled"
        $pastBookings = Booking::where('end_datetime', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();
        
        foreach ($pastBookings as $booking) {
            // Randomly set to completed or cancelled
            $newStatus = rand(0, 1) ? 'completed' : 'cancelled';
            $booking->update(['status' => $newStatus]);
            $this->command->info("Updated booking #{$booking->booking_id} from '{$booking->getOriginal('status')}' to '{$newStatus}'");
        }
        
        $this->command->info("Fixed {$pastBookings->count()} past bookings");
        
        $this->command->info('Fixing existing cancellation requests...');
        
        // Get ALL cancellation requests that are not in final states (refunded/rejected)
        $requests = CancellationRequest::whereNotIn('status', ['refunded', 'rejected'])->get();
        
        $this->command->info("Found {$requests->count()} requests to fix (not in final states)");
        
        if ($requests->count() > 0) {
            foreach ($requests as $req) {
                $oldStatus = $req->status;
                // Randomly assign to refunded or rejected
                $newStatus = ($req->request_id % 2 == 0) ? 'refunded' : 'rejected';
                
                if ($newStatus === 'refunded') {
                    $req->update([
                        'status' => 'refunded',
                        'refund_amount' => $req->refund_amount ?? ($req->booking->deposit_amount ?? 50.00),
                        'refund_reference' => $req->refund_reference ?? ('REF' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT)),
                        'refunded_at' => $req->refunded_at ?? ($req->processed_at ?? now()),
                        'processed_at' => $req->processed_at ?? now(),
                    ]);
                } else {
                    $req->update([
                        'status' => 'rejected',
                        'processed_at' => $req->processed_at ?? now(),
                    ]);
                }
                
                $this->command->info("Updated request #{$req->request_id} from '{$oldStatus}' to '{$newStatus}'");
            }
            
            $this->command->info("Fixed {$requests->count()} cancellation requests");
        } else {
            $this->command->info("All cancellation requests are already in final states (refunded/rejected)");
        }
        
        $this->command->info('Data fix completed!');
    }
}
