<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'van' records to 'mpv'
        \DB::table('cars')
            ->where('car_type', 'van')
            ->update(['car_type' => 'mpv']);

        // Modify the enum to change 'van' to 'mpv'
        \DB::statement("ALTER TABLE cars MODIFY COLUMN car_type ENUM('hatchback', 'sedan', 'suv', 'mpv') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update existing 'mpv' records back to 'van'
        \DB::table('cars')
            ->where('car_type', 'mpv')
            ->update(['car_type' => 'van']);

        // Revert the enum back to 'van'
        \DB::statement("ALTER TABLE cars MODIFY COLUMN car_type ENUM('hatchback', 'sedan', 'suv', 'van') NULL");
    }
};
