<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing bookings table if it exists with old structure
        Schema::dropIfExists('bookings');
        
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('pickup_location_id');
            $table->unsignedBigInteger('dropoff_location_id');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->integer('rental_hours');
            $table->decimal('base_price', 10, 2);
            $table->decimal('promo_discount', 10, 2)->default(0.00);
            $table->decimal('voucher_discount', 10, 2)->default(0.00);
            $table->decimal('total_rental_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('deposit_used_amount', 10, 2)->default(0.00);
            $table->decimal('deposit_refund_amount', 10, 2)->default(0.00);
            $table->enum('deposit_decision', ['refund', 'carry_forward', 'burn'])->nullable();
            $table->timestamp('agreement_signed_at')->nullable();
            $table->decimal('final_amount', 10, 2);
            $table->enum('status', ['created', 'confirmed', 'cancelled', 'active', 'completed'])->default('created');
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
            $table->foreign('pickup_location_id')->references('location_id')->on('car_locations')->onDelete('restrict');
            $table->foreign('dropoff_location_id')->references('location_id')->on('car_locations')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
