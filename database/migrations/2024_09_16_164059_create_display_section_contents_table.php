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
        Schema::create('display_section_contents', function (Blueprint $table) {
            $table->id();
            $table->integer('section_id');
            $table->enum('item_type',['product','category'])->default('product');
            $table->integer('item_id');
            $table->text('item_detail');
            $table->string('attechment')->nullable();
            $table->tinyInteger('priority')->default(0)->comment('0 = Not Visiable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_section_contents');
    }
};
