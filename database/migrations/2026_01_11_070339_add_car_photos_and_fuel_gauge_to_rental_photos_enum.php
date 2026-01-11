<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the photo_type ENUM to include car side photos and fuel gauge
        DB::statement("ALTER TABLE rental_photos MODIFY COLUMN photo_type ENUM('before', 'after', 'key', 'damage', 'other', 'agreement', 'pickup', 'parking', 'car_front', 'car_back', 'car_left', 'car_right', 'fuel_gauge') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous ENUM values (without car photos and fuel gauge)
        DB::statement("ALTER TABLE rental_photos MODIFY COLUMN photo_type ENUM('before', 'after', 'key', 'damage', 'other', 'agreement', 'pickup', 'parking') NOT NULL");
    }
};
