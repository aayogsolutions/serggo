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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('brand_id')->nullable();
            $table->text('brand_name')->nullable();
            $table->string('brandname_if_other')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('vender_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->double('price')->default(0);
            $table->text('variations')->nullable();
            $table->text('tags')->nullable();
            $table->decimal('tax',8,2)->default(0);
            $table->string('tax_type')->default('precent');
            $table->tinyInteger('status')->default(2)->comment('0 = active | 1 = inactive | 2 = pending | 3 = rejected');
            $table->string('attributes')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('sub_category_id')->nullable();
            $table->text('choice_options')->nullable();
            $table->decimal('discount',8,2)->default(0);
            $table->string('discount_type')->default('precent');
            $table->string('unit')->default('pc');
            $table->bigInteger('total_stock')->default(0);
            $table->bigInteger('total_sale')->default(0);
            $table->tinyInteger('is_featured')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
