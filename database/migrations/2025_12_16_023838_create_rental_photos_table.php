<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_photos', function (Blueprint $table) {
            $table->id('photo_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('uploaded_by_user_id'); // Can be customer_id or staff_id
            $table->enum('uploaded_by_role', ['customer', 'staff']);
            $table->enum('photo_type', ['before', 'after', 'key', 'damage', 'other']);
            $table->string('photo_url', 255);
            $table->dateTime('taken_at');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_photos');
    }
};
