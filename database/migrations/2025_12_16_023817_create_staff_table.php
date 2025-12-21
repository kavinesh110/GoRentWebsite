<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_id');
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->enum('role', ['staff', 'admin', 'manager'])->default('staff');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
