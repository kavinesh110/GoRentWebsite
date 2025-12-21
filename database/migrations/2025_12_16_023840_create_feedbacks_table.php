<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('customer_id');
            $table->tinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('reported_issue')->default(false);
            $table->timestamps();

            $table->unique('booking_id');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
