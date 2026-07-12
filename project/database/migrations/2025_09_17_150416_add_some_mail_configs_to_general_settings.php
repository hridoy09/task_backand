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
                'mailgun_domain',
                'mailgun_secret',
                'ses_access_key',
                'ses_secret_key',
                'ses_region',
                'current_sms_provider',
                'nexmo_api_key',
                'nexmo_api_secret',
                'twilio_account_sid',
                'twilio_auth_token',
                'twilio_from_number',
                'custom_sms_api_url',
                'custom_sms_api_user',
                'custom_sms_api_password',
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
                'mailgun_domain',
                'mailgun_secret',
                'ses_access_key',
                'ses_secret_key',
                'ses_region',
                'current_sms_provider',
                'nexmo_api_key',
                'nexmo_api_secret',
                'twilio_account_sid',
                'twilio_auth_token',
                'twilio_from_number',
                'custom_sms_api_url',
                'custom_sms_api_user',
                'custom_sms_api_password',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('general_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
