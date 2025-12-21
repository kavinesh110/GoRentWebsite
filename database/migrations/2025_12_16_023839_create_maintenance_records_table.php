<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id('maintenance_id');
            $table->unsignedBigInteger('car_id');
            $table->date('service_date');
            $table->text('description')->nullable();
            $table->integer('mileage_at_service');
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
