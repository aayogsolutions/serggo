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
        Schema::create('display_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('ui_type',['user_product','user_service','vender_service'])->default('user_product');
            $table->integer('category_id');
            $table->text('category_detail');
            $table->string('attechment');
            $table->tinyInteger('status')->default(0)->comment('0 = active | 1 = inactive');
            $table->tinyInteger('priority')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_categories');
    }
};
