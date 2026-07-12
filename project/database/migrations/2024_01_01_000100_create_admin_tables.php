<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('image')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_logins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->default(0)->index();
            $table->string('session_id', 100)->nullable()->unique();
            $table->string('ip')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->string('details')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_password_resets');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admin_logins');
        Schema::dropIfExists('admins');
    }
};
