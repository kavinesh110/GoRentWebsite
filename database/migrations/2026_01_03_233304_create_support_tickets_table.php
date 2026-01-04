<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id('ticket_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings', 'booking_id')->onDelete('set null');
            $table->foreignId('car_id')->nullable()->constrained('cars', 'id')->onDelete('set null');
            $table->unsignedBigInteger('maintenance_record_id')->nullable();
            $table->foreign('maintenance_record_id')->references('maintenance_id')->on('maintenance_records')->onDelete('set null');
            
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('category', ['booking', 'payment', 'car_issue', 'account', 'other'])->default('other');
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('staff_response')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('staff', 'staff_id')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->timestamps();
            
            $table->index('customer_id');
            $table->index('status');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
