<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residential_colleges', function (Blueprint $table) {
            $table->id('college_id');
            $table->string('name', 100);
            $table->string('code', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residential_colleges');
    }
};
