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
            $table->tinyInteger('registration')->default(1)->comment('0 = registered | 1 = Not registered');
            $table->tinyInteger('number_verfiy')->default(1)->comment('0 = verify | 1 = Not verify');
            $table->bigInteger('aadhar_no')->nullable();
            $table->text('aadhar_document')->nullable();
            $table->date('dob')->nullable();
            $table->enum('delivery_type',['small' , 'large'])->nullable();
            $table->string('category')->nullable();
            $table->string('password')->nullable();
            $table->mediumText('kyc_remark')->nullable();
            $table->tinyInteger('delivery_choice')->default(1)->comment('0 = accept | 1 = not reject');
            $table->enum('role',['0' , '1'])->comment('0 = vender | 1 = service');
            $table->decimal('wallet_balance',24,2)->default(0);
            $table->string('business_name')->nullable();
            $table->string('gst_no')->nullable();
            $table->mediumText('address')->nullable();
            $table->string('working_days')->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->tinyInteger('is_verify',10)->default(0)->comment('0 = not submit | 1 = pending | 2 = verified | 3 = rejected');
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
