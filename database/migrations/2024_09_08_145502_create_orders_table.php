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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('branch_id');
            $table->enum('order_type',['goods','service']);
            $table->decimal('order_amount',24,2);
            $table->string('order_status')->default('pending');
            $table->enum('order_approval',['pending','accepted','rejected'])->default('pending');
            $table->enum('editable',[0,1,2])->default(0)->comment('0 => N/A|| 1 => Edit processing || 2=> edit Accepted');
            $table->enum('edit_status',['pending','accepted','rejected'])->nullable();
            $table->decimal('before_edit_order_amount',24,2)->nullable();
            $table->decimal('backup_amount',24,2)->nullable();
            $table->decimal('order_edit_refund',24,2)->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount_amount',24,2)->default(0);
            $table->string('coupon_discount_title')->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->decimal('total_tax_amount',24,2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->bigInteger('delivery_address_id')->nullable();
            $table->tinyInteger('checked')->nullable();
            $table->tinyInteger('delivered_by')->nullable();
            $table->decimal('delivery_charge',8,2)->default(0);
            $table->text('order_note')->nullable();
            $table->bigInteger('vender_id')->nullable();
            $table->bigInteger('service_man_id')->nullable();
            $table->date('date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('callback')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('payment_by')->nullable();
            $table->string('payment_note')->nullable();
            $table->double('free_delivery_amount',8,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
