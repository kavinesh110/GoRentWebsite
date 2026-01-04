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
        Schema::table('inspections', function (Blueprint $table) {
            // Add missing columns for the new inspection workflow
            if (!Schema::hasColumn('inspections', 'car_id')) {
                $table->foreignId('car_id')->nullable()->after('booking_id')->constrained('cars', 'id')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('inspections', 'type')) {
                $table->enum('type', ['before', 'after'])->default('before')->after('car_id');
            }
            
            if (!Schema::hasColumn('inspections', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->after('type');
            }
            
            if (!Schema::hasColumn('inspections', 'exterior_condition')) {
                $table->text('exterior_condition')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('inspections', 'interior_condition')) {
                $table->text('interior_condition')->nullable()->after('exterior_condition');
            }
            
            if (!Schema::hasColumn('inspections', 'engine_condition')) {
                $table->text('engine_condition')->nullable()->after('interior_condition');
            }
            
            if (!Schema::hasColumn('inspections', 'mileage_reading')) {
                $table->integer('mileage_reading')->nullable()->after('fuel_level');
            }
            
            if (!Schema::hasColumn('inspections', 'damages_found')) {
                $table->text('damages_found')->nullable()->after('mileage_reading');
            }
            
            if (!Schema::hasColumn('inspections', 'photos')) {
                $table->json('photos')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('inspections', 'inspected_at')) {
                $table->timestamp('inspected_at')->nullable()->after('inspected_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->dropColumn([
                'car_id', 'type', 'status', 'exterior_condition', 'interior_condition',
                'engine_condition', 'mileage_reading', 'damages_found', 'photos', 'inspected_at'
            ]);
        });
    }
};
