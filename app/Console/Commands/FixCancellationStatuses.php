<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CancellationRequest;

class FixCancellationStatuses extends Command
{
    protected $signature = 'fix:cancellation-statuses';
    protected $description = 'Fix cancellation requests: change approved/pending to refunded/rejected';

    public function handle()
    {
        $this->info('Fixing cancellation request statuses...');
        
        // Get all approved or pending cancellation requests
        $requests = CancellationRequest::whereIn('status', ['approved', 'pending'])->get();
        
        $this->info("Found {$requests->count()} requests to fix");
        
        foreach ($requests as $request) {
            $oldStatus = $request->status;
            
            // Randomly assign to refunded or rejected
            $newStatus = rand(0, 1) ? 'refunded' : 'rejected';
            
            if ($newStatus === 'refunded') {
                $request->update([
                    'status' => 'refunded',
                    'refund_amount' => $request->refund_amount ?? $request->booking->deposit_amount ?? 50.00,
                    'refund_reference' => $request->refund_reference ?? 'REF' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'refunded_at' => $request->refunded_at ?? ($request->processed_at ?? now()),
                    'processed_at' => $request->processed_at ?? now(),
                ]);
            } else {
                $request->update([
                    'status' => 'rejected',
                    'processed_at' => $request->processed_at ?? now(),
                ]);
            }
            
            $this->line("Updated request #{$request->request_id} from '{$oldStatus}' to '{$newStatus}'");
        }
        
        $this->info("Fixed {$requests->count()} cancellation requests!");
        
        return 0;
    }
}
