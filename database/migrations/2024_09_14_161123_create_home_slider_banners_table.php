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
        Schema::create('home_slider_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('ui_type',['user_product','user_service','amc','vendor']);
            $table->enum('item_type',['product','category'])->nullable();
            $table->integer('item_id')->nullable();
            $table->text('item_detail')->nullable();
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
        Schema::dropIfExists('home_slider_banners');
    }
};
