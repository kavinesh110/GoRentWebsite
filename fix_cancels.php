<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\CancellationRequest;

$results = DB::select("SELECT request_id, status FROM cancellation_requests");
echo "Current statuses:\n";
foreach($results as $r) {
    echo "ID: {$r->request_id} Status: {$r->status}\n";
}

// Update all approved to refunded or rejected
$approved = CancellationRequest::where('status', 'approved')->get();
echo "\nFound {$approved->count()} approved requests\n";

foreach($approved as $req) {
    $newStatus = rand(0, 1) ? 'refunded' : 'rejected';
    
    if ($newStatus === 'refunded') {
        $req->update([
            'status' => 'refunded',
            'refund_amount' => $req->refund_amount ?? $req->booking->deposit_amount ?? 50.00,
            'refund_reference' => $req->refund_reference ?? 'REF' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'refunded_at' => $req->refunded_at ?? ($req->processed_at ?? now()),
            'processed_at' => $req->processed_at ?? now(),
        ]);
    } else {
        $req->update([
            'status' => 'rejected',
            'processed_at' => $req->processed_at ?? now(),
        ]);
    }
    
    echo "Updated #{$req->request_id} to {$newStatus}\n";
}

echo "\nDone!\n";
