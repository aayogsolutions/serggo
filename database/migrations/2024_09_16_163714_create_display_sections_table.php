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
        Schema::create('display_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('ui_type',['user_product','user_service','vender_service']);
            $table->enum('section_type',['slider','cart','box_section']);
            $table->tinyInteger('status')->default(1)->comment('0 = active | 1 = inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_sections');
    }
};
