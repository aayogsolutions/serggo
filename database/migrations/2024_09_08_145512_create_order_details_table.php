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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('alt_product_id')->default(0);
            $table->bigInteger('alt_product_qyt')->default(0);
            $table->text('alt_product_details')->nullable();
            $table->enum('alt_product_status',['pending','accepted','rejected'])->nullable();
            $table->decimal('price',8,2)->default(0);
            $table->integer('quantity')->default(0);
            $table->decimal('tax_amount',8,2)->default(0);
            $table->text('product_details')->nullable();
            $table->text('variation')->nullable();
            $table->string('variant')->nullable();
            $table->string('unit')->nullable();
            $table->bigInteger('service_man_id')->nullable();
            $table->decimal('discount_on_product',8,2)->nullable();
            $table->string('discount_type')->default('amount');
            $table->tinyInteger('is_stock_decreased')->default(0)->comment('0 = decreased | 1 = not_decreased');
            $table->tinyInteger('installation')->default(1)->comment('0 = required | 1 = not required');
            $table->decimal('installastion_amount',15,2)->default(0);
            $table->enum('gst_status',['included','excluded'])->default('excluded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
