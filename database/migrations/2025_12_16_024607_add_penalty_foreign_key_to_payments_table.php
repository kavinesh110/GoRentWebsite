<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key to payments table after penalties table is created
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('penalty_id')->references('penalty_id')->on('penalties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['penalty_id']);
        });
    }
};
