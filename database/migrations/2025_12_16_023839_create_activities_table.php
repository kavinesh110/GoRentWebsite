<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('staff_id')->on('staff')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
