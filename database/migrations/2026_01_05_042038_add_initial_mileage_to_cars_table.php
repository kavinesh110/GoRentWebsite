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
        Schema::table('cars', function (Blueprint $table) {
            // Only add the column if it doesn't already exist
            if (!Schema::hasColumn('cars', 'initial_mileage')) {
                $table->integer('initial_mileage')->default(0)->after('status')->comment('Mileage when car was first registered with Hasta');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Only drop the column if it exists
            if (Schema::hasColumn('cars', 'initial_mileage')) {
                $table->dropColumn('initial_mileage');
            }
        });
    }
};
