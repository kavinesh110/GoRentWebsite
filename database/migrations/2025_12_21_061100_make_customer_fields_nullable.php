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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
            $table->string('ic_url', 255)->nullable()->change();
            $table->string('utmid_url', 255)->nullable()->change();
            $table->string('license_url', 255)->nullable()->change();
            $table->enum('utm_role', ['student', 'staff'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone', 20)->nullable(false)->change();
            $table->string('ic_url', 255)->nullable(false)->change();
            $table->string('utmid_url', 255)->nullable(false)->change();
            $table->string('license_url', 255)->nullable(false)->change();
            $table->enum('utm_role', ['student', 'staff'])->nullable(false)->change();
        });
    }
};
