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
        Schema::create('social_login_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('image')->nullable();
            $table->json('config')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('show_in_frontend')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_login_configs');
    }
};
