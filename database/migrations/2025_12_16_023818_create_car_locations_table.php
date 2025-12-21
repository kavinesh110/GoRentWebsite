<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->string('name', 100);
            $table->enum('type', ['pickup', 'dropoff', 'both']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_locations');
    }
};
