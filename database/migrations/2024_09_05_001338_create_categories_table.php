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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('parent_id')->comment('0 => Category | 1< => SubCategory');
            $table->integer('position')->comment('0 => Category | 1 => SubCategory');
            $table->tinyInteger('status',false)->default(0)->comment('0 => Active | 1 => Inactive');
            $table->string('image')->default('def.png');
            $table->tinyInteger('priority')->default(1);
            $table->tinyInteger('is_installable')->default(1)->commet('0 = active | 1 = inactive');
            $table->decimal('commission',8,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
