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
        // Modify the status ENUM to include 'verified' and 'deposit_returned'
        // MySQL requires raw SQL to modify ENUM columns
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('created', 'verified', 'confirmed', 'cancelled', 'active', 'completed', 'deposit_returned') DEFAULT 'created'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values (without verified and deposit_returned)
        // Note: This will fail if there are bookings with 'verified' or 'deposit_returned' status
        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `status` ENUM('created', 'confirmed', 'cancelled', 'active', 'completed') DEFAULT 'created'");
    }
};
