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
        // Remove 'engine_problems' from the category enum
        // MySQL doesn't support direct enum modification, so we need to alter the column
        DB::statement("ALTER TABLE support_tickets MODIFY category ENUM('cleanliness', 'lacking_facility', 'bluetooth', 'engine', 'others') DEFAULT 'others'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to include engine_problems
        DB::statement("ALTER TABLE support_tickets MODIFY category ENUM('cleanliness', 'engine_problems', 'lacking_facility', 'bluetooth', 'engine', 'others') DEFAULT 'others'");
    }
};
