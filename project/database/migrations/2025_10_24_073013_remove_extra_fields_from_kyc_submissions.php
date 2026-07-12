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
        Schema::table('kyc_submissions', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'document_number', 'document_front', 'document_back']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_submissions', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('document_number')->nullable();
            $table->string('document_front')->nullable();
            $table->string('document_back')->nullable();
        });
    }
};
