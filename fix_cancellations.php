<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CancellationRequest;
use Illuminate\Support\Facades\DB;

// Get all approved cancellation requests
$approved = CancellationRequest::where('status', 'approved')->get();

echo "Found {$approved->count()} approved cancellation requests\n";

foreach ($approved as $request) {
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
    
    echo "Updated request #{$request->request_id} from 'approved' to '{$newStatus}'\n";
}

echo "Done!\n";
