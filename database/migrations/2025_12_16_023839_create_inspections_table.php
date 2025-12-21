<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id('inspection_id');
            $table->unsignedBigInteger('booking_id');
            $table->enum('inspection_type', ['pickup', 'return', 'other']);
            $table->dateTime('datetime');
            $table->tinyInteger('fuel_level'); // 0-8 bars
            $table->integer('odometer_reading');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('inspected_by');
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('inspected_by')->references('staff_id')->on('staff')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
