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
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('ui_type',['user_product','user_service','amc']);
            $table->string('attechment_type');
            $table->string('attechment');
            $table->string('background_color');
            $table->string('font_color');
            $table->tinyInteger('status')->default(1)->comment('0 = active | 1 = inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
