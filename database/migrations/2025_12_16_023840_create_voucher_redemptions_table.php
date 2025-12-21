<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_redemptions', function (Blueprint $table) {
            $table->id('redemption_id');
            $table->unsignedBigInteger('voucher_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('redeemed_at');
            $table->timestamps();

            $table->unique('booking_id');
            $table->foreign('voucher_id')->references('voucher_id')->on('vouchers')->onDelete('restrict');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_redemptions');
    }
};
