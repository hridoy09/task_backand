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
        Schema::table('general_settings', function (Blueprint $table) {
            $columns = [
                'sendgrid_api_key',
                'brevo_api_key',
                'postmark_api_token',
                'mailersend_api_key',
                'sparkpost_api_key',
                'mailjet_api_key',
                'mailjet_secret_key',
                'elastic_email_api_key',
                'smtp_com_username',
                'smtp_com_password',
                'resend_api_key',
            ];

            foreach ($columns as $column) {
                if (! Schema::hasColumn('general_settings', $column)) {
                    $table->string($column)->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $columns = [
                'sendgrid_api_key',
                'brevo_api_key',
                'postmark_api_token',
                'mailersend_api_key',
                'sparkpost_api_key',
                'mailjet_api_key',
                'mailjet_secret_key',
                'elastic_email_api_key',
                'smtp_com_username',
                'smtp_com_password',
                'resend_api_key',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('general_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
