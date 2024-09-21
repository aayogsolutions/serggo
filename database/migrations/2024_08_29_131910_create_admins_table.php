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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('number')->unique();
            $table->mediumText('image');
            $table->mediumText('identity_image')->nullable();
            $table->mediumText('identity_type')->nullable();
            $table->mediumText('identity_number')->nullable();
            $table->string('password');
            $table->tinyInteger('status', false)->default(0)->comment("0 = active | 1 = inactive");
            $table->tinyInteger('role_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
