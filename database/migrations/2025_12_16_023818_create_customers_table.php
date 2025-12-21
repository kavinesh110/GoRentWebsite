<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20);
            $table->string('password_hash', 255);
            $table->string('ic_url', 255);
            $table->string('utmid_url', 255);
            $table->string('license_url', 255);
            $table->enum('utm_role', ['student', 'staff']);
            $table->unsignedBigInteger('college_id')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->timestamp('blacklist_since')->nullable();
            $table->decimal('deposit_balance', 10, 2)->default(0.00);
            $table->integer('total_rental_hours')->default(0);
            $table->integer('total_stamps')->default(0);
            $table->timestamps();

            $table->foreign('college_id')->references('college_id')->on('residential_colleges')->onDelete('set null');
            $table->foreign('verified_by')->references('staff_id')->on('staff')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
