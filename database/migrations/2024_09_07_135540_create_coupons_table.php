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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('code',20)->nullable();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->decimal('min_purchase',8,2)->default(0);
            $table->decimal('max_purchase',8,2)->default(0);
            $table->decimal('max_discount',8,2)->default(0);
            $table->decimal('discount',8,2)->default(0);
            $table->string('discount_type')->default('percentage');
            $table->tinyInteger('status')->default(0)->comment('0 = Active | 1 = Inactive');
            $table->string('coupon_type')->nullable();
            $table->integer('limit')->nullable();
            $table->integer('customer_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
