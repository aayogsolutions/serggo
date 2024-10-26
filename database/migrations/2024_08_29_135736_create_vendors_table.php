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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('number')->unique()->nullable();
            $table->string('otp')->nullable();
            $table->mediumText('image')->nullable();
            $table->timestamp('otp_expired_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role',['0' , '1'])->comment('0 = vender | 1 = service');
            $table->double('wallet_balance')->default(0);
            $table->tinyInteger('is_verify',10)->default(1)->comment('0 = verify | 1 = unverify');
            $table->string('referral_code')->unique();
            $table->string('referred_by')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->tinyInteger('is_block')->default(0)->comment('0 = active | 1 = inactive');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
