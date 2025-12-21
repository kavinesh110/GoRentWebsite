<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id('penalty_id');
            $table->unsignedBigInteger('booking_id');
            $table->enum('penalty_type', ['late', 'fuel', 'damage', 'accident', 'other']);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_installment')->default(false);
            $table->enum('status', ['pending', 'partially_paid', 'settled'])->default('pending');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
