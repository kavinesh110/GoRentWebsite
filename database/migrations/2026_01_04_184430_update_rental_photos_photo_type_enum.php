<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the photo_type ENUM to include 'agreement' and 'pickup'
        // Using MODIFY COLUMN to update the ENUM values
        DB::statement("ALTER TABLE rental_photos MODIFY COLUMN photo_type ENUM('before', 'after', 'key', 'damage', 'other', 'agreement', 'pickup', 'parking') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE rental_photos MODIFY COLUMN photo_type ENUM('before', 'after', 'key', 'damage', 'other') NOT NULL");
    }
};
