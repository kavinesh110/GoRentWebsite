<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('penalty_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_type', ['deposit', 'rental', 'penalty', 'refund', 'installment']);
            $table->enum('payment_method', ['bank_transfer', 'cash', 'other']);
            $table->string('receipt_url', 255);
            $table->dateTime('payment_date');
            $table->enum('status', ['pending', 'verified'])->default('pending');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
