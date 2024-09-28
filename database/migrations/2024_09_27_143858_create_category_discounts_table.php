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
        Schema::create('category_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('category_id')->unique();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('discount_type')->nullable();
            $table->float('discount_amount')->default(0);
            $table->float('maximum_amount')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_discounts');
    }
};
