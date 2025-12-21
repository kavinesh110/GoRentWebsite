<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Drop old columns if they exist
            $table->dropColumn(['name', 'description', 'price_per_day', 'exterior_image', 'interior_image', 'features']);
        });

        Schema::table('cars', function (Blueprint $table) {
            // Add new columns matching the schema
            $table->string('plate_number', 20)->unique()->after('id');
            $table->string('brand', 50)->after('plate_number');
            $table->string('model', 50)->after('brand');
            $table->enum('fuel_type', ['petrol', 'diesel', 'hybrid', 'ev', 'other'])->after('model');
            $table->smallInteger('year')->after('fuel_type');
            $table->decimal('base_rate_per_hour', 10, 2)->after('year');
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available')->after('base_rate_per_hour');
            $table->integer('current_mileage')->default(0)->after('status');
            $table->integer('service_mileage_limit')->after('current_mileage');
            $table->date('last_service_date')->nullable()->after('service_mileage_limit');
            $table->string('image_url', 255)->nullable()->after('last_service_date');
            $table->boolean('gps_enabled')->default(false)->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'plate_number', 'brand', 'model', 'fuel_type', 'year',
                'base_rate_per_hour', 'status', 'current_mileage',
                'service_mileage_limit', 'last_service_date', 'image_url', 'gps_enabled'
            ]);
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_day', 10, 2);
            $table->string('exterior_image')->nullable();
            $table->string('interior_image')->nullable();
            $table->json('features')->nullable();
        });
    }
};
