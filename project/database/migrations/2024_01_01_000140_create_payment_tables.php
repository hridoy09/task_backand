<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true)->comment('1=active,0=inactive');
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('is_test_mode')->default(false);
            $table->json('config')->nullable();
            $table->string('currency', 40)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->string('key');
            $table->boolean('manual')->default(false);
            $table->string('short_desc')->nullable();
            $table->text('instruction')->nullable();

            $table->unique('key');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->comment('pending, success, failed, refunded');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('usd');
            $table->string('method')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->string('transaction_no');
            $table->unsignedBigInteger('payment_gateway_id')->default(0);
            $table->timestamp('paid_at')->nullable();

            $table->unique('transaction_no');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
    }
};
