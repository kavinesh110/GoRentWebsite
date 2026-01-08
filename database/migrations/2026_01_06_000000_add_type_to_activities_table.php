<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('type', ['schedule', 'promotion'])->default('schedule')->after('description');
        });
        
        // Set existing activities with image_url as promotions, others as schedules
        \DB::statement("UPDATE activities SET type = CASE WHEN image_url IS NOT NULL THEN 'promotion' ELSE 'schedule' END");
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
