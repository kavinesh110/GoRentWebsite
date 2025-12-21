<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id('voucher_id');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->integer('min_stamps_required')->default(0);
            $table->date('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
