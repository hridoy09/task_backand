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
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('subject');
            $table->longText('body');
            $table->json('shortcodes')->nullable();
            $table->json('attachments')->nullable();
            $table->string('view')->default('global');
            $table->timestamps();
        });

        DB::table('mail_templates')->insert([
            [
                'name'       => 'User Registered',
                'code'       => 'USER_REGISTERED',
                'subject'    => 'Welcome to [app_name], [user_name]!',
                'body'       => '<p>Hello [user_name],</p><p>Thank you for creating an account with [app_name]. We are excited to have you with us.</p><p>You can log in any time at [app_url].</p><p>Regards,<br>[app_name] Team</p>',
                'shortcodes' => json_encode([
                    ['key' => '[user_name]', 'description' => 'Full name of the user'],
                    ['key' => '[app_url]', 'description' => 'Application URL'],
                ]),
                'attachments' => json_encode([]),
                'view'        => 'global',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'       => 'Password Reset',
                'code'       => 'PASSWORD_RESET_REQUEST',
                'subject'    => 'Reset your password for [app_name]',
                'body'       => '<p>Hello [user_name],</p><p>You recently requested to reset your password. Click the button below to proceed:</p><p><a href="[reset_link]" style="background:#0d6efd;color:#ffffff;padding:10px 20px;border-radius:4px;display:inline-block;text-decoration:none;">Reset Password</a></p><p>If you did not request this change, please ignore this email.</p><p>Regards,<br>[app_name] Team</p>',
                'shortcodes' => json_encode([
                    ['key' => '[user_name]', 'description' => 'Full name of the user'],
                    ['key' => '[reset_link]', 'description' => 'Reset password URL'],
                ]),
                'attachments' => json_encode([]),
                'view'        => 'global',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
