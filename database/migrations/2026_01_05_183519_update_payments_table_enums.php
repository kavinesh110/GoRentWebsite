<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'full_payment' to payment_type and 'e-wallet' to payment_method
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('deposit', 'rental', 'penalty', 'refund', 'installment', 'full_payment') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash', 'other', 'e-wallet') NOT NULL");
    }

    public function down(): void
    {
        // Revert changes
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('deposit', 'rental', 'penalty', 'refund', 'installment') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash', 'other') NOT NULL");
    }
};
