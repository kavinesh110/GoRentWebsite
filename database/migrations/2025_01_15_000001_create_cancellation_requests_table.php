<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for cancellation_requests table
 * Stores customer requests to cancel their bookings with refund details
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellation_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('customer_id');
            
            // Cancellation reason
            $table->enum('reason_type', [
                'change_of_plans',
                'found_alternative',
                'financial_reasons',
                'emergency',
                'vehicle_issue',
                'service_issue',
                'other'
            ]);
            $table->text('reason_details')->nullable(); // Additional explanation
            
            // Refund bank details
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_holder', 100);
            
            // Supporting documents (optional)
            $table->string('proof_document_url')->nullable();
            
            // Request status
            $table->enum('status', [
                'pending',      // Waiting for staff review
                'approved',     // Cancellation approved
                'rejected',     // Cancellation rejected
                'refunded'      // Refund has been processed
            ])->default('pending');
            
            // Staff response
            $table->unsignedBigInteger('processed_by_staff_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('staff_notes')->nullable();
            
            // Refund details
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_reference')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellation_requests');
    }
};
