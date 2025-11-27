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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('merchant_transaction_id')->unique();
            $table->string('ntt_data_transaction_id')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['initiated', 'pending', 'success', 'failed', 'cancelled'])->default('initiated');
            $table->string('payment_mode')->nullable();
            $table->text('response_data')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->timestamp('transaction_completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
