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
            $table->unsignedInteger('user_id');
            $table->enum('network_provider', ['shago', 'bap'])->nullable();
            $table->enum('transaction_type', ['wallet_debit', 'wallet_credit', 'comission_top_up', 'wallet_top_up']);
            $table->decimal('amount',16,2)->default(0);
            $table->string('reference');
            $table->string('description')->nullable();
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
