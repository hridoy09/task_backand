<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('department_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');

            // 0 = closed, 1 = open
            $table->boolean('status')->default(1);

            // Optional extras to match your style
            $table->integer('added_by')->nullable()->default(0); // admin()->id
            $table->json('meta')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
