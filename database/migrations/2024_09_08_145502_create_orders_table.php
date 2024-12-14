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
            $table->id()->startingValue(1000000);
            $table->bigInteger('user_id');
            $table->enum('order_type',['goods','service']);
            $table->decimal('order_amount',24,2);
            $table->decimal('item_total',24,2);
            $table->string('order_status')->default('pending')->comment('[pending,confirmed,packing,out_for_delivery,delivered,canceled,returned,failed,rejected]');
            $table->enum('order_approval',['pending','accepted','rejected'])->default('pending');
            $table->enum('editable',[0,1,2])->default(0)->comment('0 => N/A|| 1 => Edit processing || 2=> edit Accepted');
            $table->enum('edit_status',['pending','accepted','rejected'])->nullable();
            $table->decimal('before_edit_order_amount',24,2)->nullable();
            $table->decimal('backup_amount',24,2)->nullable();
            $table->decimal('order_edit_refund',24,2)->nullable();
            $table->string('payment_status')->default('unpaid');
            $table->decimal('total_tax_amount',24,2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->bigInteger('delivery_address_id')->nullable();
            $table->tinyInteger('checked')->nullable();
            $table->tinyInteger('delivered_by')->default(0)->comment('0 = admin| 1 = vendor');
            $table->bigInteger('deliveryman_id')->nullable()->comment('If Admin Accept then deliveryman id will be store here');
            $table->tinyInteger('accepted_by')->default(0)->comment('0 = admin| 1 = vendor');
            $table->decimal('delivery_charge',8,2)->default(0);
            $table->decimal('total_discount',8,2)->default(0);
            $table->decimal('total_installation',8,2)->default(0);
            $table->string('gst_no')->nullable();
            $table->string('gst_name')->nullable();
            $table->integer('mobile_no')->nullable();
            $table->enum('tax_type',['included','excluded'])->default('excluded');
            $table->enum('order_category',['small','large'])->nullable();
            $table->tinyInteger('free_delivery')->default(1);
            $table->tinyInteger('deliveryman_status')->default(1)->comment('0 = accpeted | 1 = pending');
            $table->decimal('delivery_charge',8,2)->default(0);
            $table->text('order_note')->nullable();
            $table->bigInteger('vender_id')->nullable();
            $table->string('coupon_code')->nullable();
            $table->date('date')->nullable();
            $table->time('servicetime')->nullable();
            $table->date('delivery_date')->nullable();
            $table->integer('delivery_timeslot_id')->nullable();
            $table->string('callback')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('payment_by')->nullable();
            $table->string('payment_note')->nullable();
            $table->text('partial_payment')->nullable();
            $table->double('free_delivery_amount',8,2)->default(0);
            $table->tinyInteger('gst_invoice')->default(1)->comment("0 = Required | 1 = Not required");
            $table->decimal('advance_payment',8,2)->default(0);
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
