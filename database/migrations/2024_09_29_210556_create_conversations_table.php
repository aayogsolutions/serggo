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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->biginteger('user_id');
            $table->text('message')->nullable()->change();
            $table->text('reply')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->boolean('is_reply')->default('0');
            $table->tinyinteger('checked')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
