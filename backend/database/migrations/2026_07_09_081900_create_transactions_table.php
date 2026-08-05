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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->onDelete('set null');
            $table->date('transaction_date');
            $table->enum('transaction_type', ['income', 'expense'])->default('income');
            $table->decimal('amount', 10, 2);
            $table->date('delivery_date')->nullable();
            $table->enum('responsible',['edgar','jorge'])->default('edgar');
            $table->enum('status', ['paid', 'pending', 'partially_paid'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
