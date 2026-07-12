<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->default(0)->nullable();
            // who replied
            $table->unsignedBigInteger('admin_id')->nullable();   // if an admin replied
            $table->unsignedBigInteger('user_id')->nullable();    // if a user replied (optional, if you have users)
            $table->boolean('is_admin')->default(true);           // true=admin, false=user
            $table->longText('message');
            $table->json('attachments')->nullable();              // keep file paths as array if you want
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
    }
};
