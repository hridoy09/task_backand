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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0)->nullable();
            $table->integer('payment_id')->default(0)->nullable();
            $table->string('trx')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('details')->nullable();
            $table->string('trx_type', 10)->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->decimal('balance_before', 28, 8)->default(0);
            $table->decimal('balance_after', 28, 8)->default(0);
            $table->string('remark')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
