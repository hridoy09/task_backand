<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // 0=low,1=normal,2=high,3=urgent
            $table->boolean('priority')->default(1)->after('status');

            // 0=closed,1=open,2=answered,3=pending
            $table->boolean('status')->default(1)->change();

            // thread meta
            $table->unsignedBigInteger('last_replied_by')->nullable()->after('added_by');
            $table->timestamp('last_replied_at')->nullable()->after('last_replied_by');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['priority', 'last_replied_by', 'last_replied_at']);
            // cannot revert status enum cleanly; leave as is
        });
    }
};
