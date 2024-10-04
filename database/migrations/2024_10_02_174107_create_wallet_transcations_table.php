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
        Schema::create('wallet_transcations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('transactions_id');
            $table->decimal('credit',24,2);
            $table->decimal('debit',24,2);
            $table->string('transactions_type');
            $table->string('reference');
            $table->decimal('balance',24,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transcations');
    }
};
