<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_logins', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_logins', 'session_id')) {
                $table->string('session_id', 100)->nullable()->after('admin_id');
                $table->unique('session_id', 'admin_logins_session_id_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin_logins', function (Blueprint $table) {
            if (Schema::hasColumn('admin_logins', 'session_id')) {
                $table->dropUnique('admin_logins_session_id_unique');
                $table->dropColumn('session_id');
            }
        });
    }
};
