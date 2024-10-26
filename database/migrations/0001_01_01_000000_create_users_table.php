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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('number')->unique()->nullable();
            $table->string('otp')->nullable();
            $table->mediumText('image')->nullable();
            $table->timestamp('otp_expired_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->double('wallet_balance')->default(0);
            $table->string('temporary_token')->nullable();
            $table->string('referral_code')->unique()->nullable();
            $table->string('referred_by')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->tinyInteger('is_block')->default(0)->comment("0 = no | 1 = yes");
            $table->tinyInteger('registration')->default(0)->comment("0 = Required | 1 = Not required");
            $table->tinyInteger('number_verify')->default(0)->comment("0 = Required | 1 = Not required");
            $table->string('gst_name')->nullable();
            $table->string('gst_number',30)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
