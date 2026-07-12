<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('currency_code', 10);
            $table->decimal('rate', 24, 12)->default(1);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['payment_gateway_id', 'currency_code'], 'pg_currency_unique');
        });

        $systemCurrency = DB::table('general_settings')->value('currency') ?? 'USD';

        $gateways = DB::table('payment_gateways')->select('id', 'currency')->get();

        foreach ($gateways as $gateway) {
            $code = $gateway->currency ?: $systemCurrency;

            DB::table('payment_gateway_currencies')->insert([
                'payment_gateway_id' => $gateway->id,
                'currency_code'      => $code,
                'rate'               => 1,
                'is_default'         => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            if (!$gateway->currency) {
                DB::table('payment_gateways')
                    ->where('id', $gateway->id)
                    ->update(['currency' => $code]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_currencies');
    }
};
