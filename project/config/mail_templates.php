<?php

return [
    'shortcodes' => [
        'global' => [
            '[app_name]' => 'Application name',
            '[app_url]' => 'Application base URL',
            '[support_email]' => 'Primary support email address',
            '[year]' => 'Current year (YYYY)',
            '[date]' => 'Current date (e.g. 17 Oct 2025)',
            '[time]' => 'Current time (e.g. 05:30 PM)',
        ],

        'templates' => [
            'USER_REGISTERED' => [
                '[user_name]' => 'Full name of the user',
                '[user_email]' => 'Email address of the user',
                '[login_url]' => 'Sign-in URL for the user area',
            ],
            'EMAIL_VERIFICATION' => [
                '[user_name]' => 'Full name of the user',
                '[verification_url]' => 'Signed verification link',
                '[expires_at]' => 'Verification link expiry date/time',
            ],
            'PASSWORD_RESET_REQUEST' => [
                '[user_name]' => 'Full name of the user',
                '[reset_url]' => 'Password reset URL',
                '[request_ip]' => 'IP address that initiated the reset request',
            ],
            'PASSWORD_RESET_SUCCESS' => [
                '[user_name]' => 'Full name of the user',
                '[changed_at]' => 'Timestamp when password was changed',
                '[login_url]' => 'Sign-in URL for the user area',
                '[request_ip]' => 'IP address used for the change',
            ],
            'USER_PASSWORD_CHANGED' => [
                '[user_name]' => 'Full name of the user',
                '[changed_at]' => 'Timestamp when password was changed',
                '[request_ip]' => 'IP address used for the change',
            ],
            'PROFILE_UPDATED' => [
                '[user_name]' => 'Full name of the user',
                '[updated_at]' => 'Timestamp of the profile update',
                '[profile_url]' => 'URL to the profile settings page',
                '[updated_fields]' => 'Comma separated list of fields that were changed',
            ],
            'KYC_SUBMITTED' => [
                '[user_name]' => 'Full name of the user',
                '[kyc_status]' => 'Current KYC status',
                '[kyc_submitted_at]' => 'Submission time',
                '[kyc_dashboard_url]' => 'URL to view KYC status from the user area',
            ],
            'KYC_APPROVED' => [
                '[user_name]' => 'Full name of the user',
                '[kyc_review_note]' => 'Reviewer note provided by the compliance team',
                '[kyc_dashboard_url]' => 'URL to view KYC status from the user area',
            ],
            'KYC_REJECTED' => [
                '[user_name]' => 'Full name of the user',
                '[kyc_review_note]' => 'Reviewer reason for rejection',
                '[kyc_resubmit_url]' => 'URL to resubmit updated KYC information',
            ],
            'PAYMENT_PENDING' => [
                '[user_name]' => 'Full name of the user',
                '[payment_amount]' => 'Payment amount',
                '[payment_currency]' => 'Currency of the payment',
                '[payment_method]' => 'Payment method used',
                '[transaction_no]' => 'Gateway or internal transaction number',
                '[payment_created_at]' => 'Time the payment was logged',
                '[payment_details_url]' => 'URL to view payment details',
            ],
            'PAYMENT_SUCCESS' => [
                '[user_name]' => 'Full name of the user',
                '[payment_amount]' => 'Payment amount',
                '[payment_currency]' => 'Currency of the payment',
                '[payment_method]' => 'Payment method used',
                '[transaction_no]' => 'Gateway or internal transaction number',
                '[payment_processed_at]' => 'Timestamp when payment succeeded',
                '[balance_before]' => 'Account balance before processing',
                '[balance_after]' => 'Account balance after processing',
                '[payment_receipt_url]' => 'URL to download or review the receipt',
            ],
            'PAYMENT_FAILED' => [
                '[user_name]' => 'Full name of the user',
                '[payment_amount]' => 'Payment amount',
                '[payment_currency]' => 'Currency of the payment',
                '[payment_method]' => 'Payment method used',
                '[transaction_no]' => 'Gateway or internal transaction number',
                '[failure_reason]' => 'Failure message returned from the gateway',
                '[payment_created_at]' => 'Time the payment was logged',
            ],
            'PAYMENT_REFUNDED' => [
                '[user_name]' => 'Full name of the user',
                '[refund_amount]' => 'Amount that was refunded',
                '[refund_currency]' => 'Currency of the refund',
                '[refund_reason]' => 'Reason provided for the refund',
                '[refund_processed_at]' => 'Timestamp when the refund was processed',
                '[transaction_no]' => 'Original transaction number',
            ],
            'TRANSACTION_CREATED' => [
                '[user_name]' => 'Full name of the user',
                '[trx]' => 'Transaction reference / ID',
                '[trx_type]' => 'Whether the transaction is credit or debit',
                '[amount]' => 'Transaction amount',
                '[currency]' => 'Transaction currency',
                '[balance_before]' => 'Balance before the transaction',
                '[balance_after]' => 'Balance after the transaction',
                '[transaction_details]' => 'Description of the transaction',
                '[transaction_date]' => 'Date/time of the transaction',
            ],
            'SUPPORT_TICKET_CREATED' => [
                '[user_name]' => 'Full name of the user',
                '[ticket_id]' => 'Ticket ID/reference',
                '[ticket_subject]' => 'Subject of the ticket',
                '[ticket_priority]' => 'Priority label',
                '[ticket_url]' => 'URL to view the ticket',
            ],
            'SUPPORT_TICKET_REPLY' => [
                '[user_name]' => 'Full name of the user',
                '[ticket_id]' => 'Ticket ID/reference',
                '[reply_message]' => 'Reply message content',
                '[reply_author]' => 'Name of the replier (admin or user)',
                '[ticket_url]' => 'URL to view the ticket',
            ],
            'SUPPORT_TICKET_STATUS_CHANGED' => [
                '[user_name]' => 'Full name of the user',
                '[ticket_id]' => 'Ticket ID/reference',
                '[ticket_status]' => 'New status of the ticket',
                '[ticket_url]' => 'URL to view the ticket',
            ],
            'USER_LOGIN_ALERT' => [
                '[user_name]' => 'Full name of the user',
                '[login_time]' => 'Date and time of login',
                '[login_ip]' => 'IP address used to login',
                '[login_device]' => 'Device or user agent string',
                '[login_location]' => 'Approximate location inferred from the IP',
            ],
            'ADMIN_PASSWORD_RESET_REQUEST' => [
                '[admin_name]' => 'Name of the admin',
                '[reset_url]' => 'Password reset URL',
                '[request_ip]' => 'IP address that initiated the reset request',
            ],
            'ADMIN_PROFILE_UPDATED' => [
                '[admin_name]' => 'Name of the admin',
                '[updated_at]' => 'Timestamp of the profile update',
                '[profile_url]' => 'URL to admin profile settings',
                '[updated_fields]' => 'Comma separated list of fields that were changed',
            ],
            'ADMIN_NEW_USER_REGISTERED' => [
                '[user_name]' => 'Full name of the new user',
                '[user_email]' => 'Email address of the new user',
                '[user_registered_at]' => 'Registration timestamp',
                '[user_profile_url]' => 'Admin URL to the user profile',
            ],
            'ADMIN_KYC_SUBMISSION' => [
                '[user_name]' => 'Full name of the user',
                '[kyc_status]' => 'Current KYC status',
                '[kyc_submitted_at]' => 'Submission time',
                '[kyc_review_url]' => 'Admin URL to review the submission',
            ],
            'ADMIN_PAYMENT_ALERT' => [
                '[user_name]' => 'Full name of the user',
                '[payment_amount]' => 'Payment amount',
                '[payment_currency]' => 'Currency of the payment',
                '[payment_method]' => 'Payment method used',
                '[transaction_no]' => 'Transaction number',
                '[payment_status]' => 'Current payment status',
                '[payment_created_at]' => 'Time the payment was logged',
                '[payment_admin_url]' => 'Admin URL to inspect the payment',
            ],
            'BROADCAST_NOTIFICATION' => [
                '[user_name]' => 'Full name of the recipient',
                '[subject]' => 'Subject for the broadcast message',
                '[message]' => 'Body content of the broadcast message',
            ],
        ],
    ],

    'templates' => [
        'USER_REGISTERED' => [
            'name' => 'User Registered',
            'category' => 'user',
            'description' => 'Sent to welcome a newly created user account.',
            'subject' => 'Welcome to [app_name], [user_name]!',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Welcome to [app_name]! We&#039;re excited to have you on board.</p>
<p>You can sign in any time at <a href="[login_url]">[login_url]</a>. If you need help, email us at [support_email].</p>
<p>Thanks,<br>The [app_name] Team</p>
HTML,
        ],
        'EMAIL_VERIFICATION' => [
            'name' => 'Email Verification',
            'category' => 'security',
            'description' => 'Provides the email verification link after registration.',
            'subject' => 'Verify your email for [app_name]',
            'view' => 'email-verification',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Please confirm your email address to finish setting up your [app_name] account.</p>
<p><a href="[verification_url]" style="display:inline-block;padding:12px 20px;background:#1a73e8;color:#ffffff;border-radius:4px;text-decoration:none;">Verify Email Address</a></p>
<p>This link expires on [expires_at]. If you didn&#039;t create an account, no further action is required.</p>
<p>Regards,<br>The [app_name] Team</p>
HTML,
        ],
        'PASSWORD_RESET_REQUEST' => [
            'name' => 'Password Reset Request',
            'category' => 'security',
            'description' => 'Delivered when a user requests a password reset link.',
            'subject' => 'Reset your password for [app_name]',
            'view' => 'password-reset-request',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>We received a request to reset your password. Click the button below to choose a new one.</p>
<p><a href="[reset_url]" style="display:inline-block;padding:12px 20px;background:#0d6efd;color:#ffffff;border-radius:4px;text-decoration:none;">Reset Password</a></p>
<p>If you did not request this change, you can safely ignore this email. Request originated from IP: [request_ip].</p>
<p>Thanks,<br>The [app_name] Team</p>
HTML,
        ],
        'PASSWORD_RESET_SUCCESS' => [
            'name' => 'Password Reset Success',
            'category' => 'security',
            'description' => 'Confirms that a password reset has been completed.',
            'subject' => 'Your [app_name] password was changed',
            'view' => 'password-reset-success',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Your password was changed successfully on [changed_at]. For security, we recommend reviewing your account activity.</p>
<p>If you did not make this change, reset your password immediately at <a href="[login_url]">[login_url]</a> or contact us at [support_email].</p>
<p>Request IP: [request_ip]</p>
<p>Regards,<br>The [app_name] Security Team</p>
HTML,
        ],
        'USER_PASSWORD_CHANGED' => [
            'name' => 'Password Changed (Profile)',
            'category' => 'security',
            'description' => 'Alerts users when their password is updated from profile settings.',
            'subject' => 'Your password on [app_name] has been updated',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>This is a confirmation that your password was updated on [changed_at].</p>
<p>If you did not perform this change please reset your password immediately or reach out to [support_email].</p>
<p>Change initiated from IP: [request_ip]</p>
<p>Stay secure,<br>The [app_name] Team</p>
HTML,
        ],
        'PROFILE_UPDATED' => [
            'name' => 'Profile Updated',
            'category' => 'user',
            'description' => 'Notifies users that profile information was updated.',
            'subject' => 'Your profile details were updated',
            'view' => 'profile-updated',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>We wanted to let you know that your profile was updated on [updated_at]. The following information changed: [updated_fields].</p>
<p>You can review your details here: <a href="[profile_url]">[profile_url]</a>.</p>
<p>If you didn&#039;t make these changes, contact us at [support_email].</p>
<p>Regards,<br>The [app_name] Team</p>
HTML,
        ],
        'KYC_SUBMITTED' => [
            'name' => 'KYC Submitted',
            'category' => 'compliance',
            'description' => 'Acknowledges receipt of a new KYC submission.',
            'subject' => 'We received your KYC submission',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Thanks for submitting your KYC details on [kyc_submitted_at]. Our compliance team will review your documents shortly.</p>
<p>You can check the latest status any time at <a href="[kyc_dashboard_url]">[kyc_dashboard_url]</a>. Current status: <strong>[kyc_status]</strong>.</p>
<p>We appreciate your cooperation,<br>The [app_name] Compliance Team</p>
HTML,
        ],
        'KYC_APPROVED' => [
            'name' => 'KYC Approved',
            'category' => 'compliance',
            'description' => 'Informs users that their KYC request has been approved.',
            'subject' => 'Your KYC was approved',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Great news! Your KYC submission has been approved.</p>
<p>Reviewer note: [kyc_review_note]</p>
<p>You can view the status at <a href="[kyc_dashboard_url]">[kyc_dashboard_url]</a>.</p>
<p>Thanks for verifying your identity,<br>The [app_name] Compliance Team</p>
HTML,
        ],
        'KYC_REJECTED' => [
            'name' => 'KYC Rejected',
            'category' => 'compliance',
            'description' => 'Informs users that their KYC request needs more information.',
            'subject' => 'We need more information for your KYC request',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>We reviewed your KYC submission but could not approve it yet.</p>
<p>Reviewer note: [kyc_review_note]</p>
<p>Please provide the requested updates and resubmit at <a href="[kyc_resubmit_url]">[kyc_resubmit_url]</a>.</p>
<p>We&#039;re here to help if you have questions.<br>The [app_name] Compliance Team</p>
HTML,
        ],
        'PAYMENT_PENDING' => [
            'name' => 'Payment Pending',
            'category' => 'finance',
            'description' => 'Acknowledges that a payment has been logged and awaits confirmation.',
            'subject' => 'We received your payment request',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>We registered your payment of [payment_amount] [payment_currency] via [payment_method].</p>
<p>Transaction reference: [transaction_no]</p>
<p>We&#039;ll notify you once it is processed. You can review the details at <a href="[payment_details_url]">[payment_details_url]</a>.</p>
<p>Logged at [payment_created_at]</p>
<p>Regards,<br>The [app_name] Billing Team</p>
HTML,
        ],
        'PAYMENT_SUCCESS' => [
            'name' => 'Payment Successful',
            'category' => 'finance',
            'description' => 'Confirms a successful payment and balance update.',
            'subject' => 'Payment confirmed — [transaction_no]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Your payment of [payment_amount] [payment_currency] via [payment_method] was successful.</p>
<p>Transaction: [transaction_no]</p>
<p>Previous balance: [balance_before]<br>New balance: [balance_after]</p>
<p>Processed on [payment_processed_at]. You can download your receipt at <a href="[payment_receipt_url]">[payment_receipt_url]</a>.</p>
<p>Thank you for your business!<br>The [app_name] Billing Team</p>
HTML,
        ],
        'PAYMENT_FAILED' => [
            'name' => 'Payment Failed',
            'category' => 'finance',
            'description' => 'Explains that a payment attempt was unsuccessful.',
            'subject' => 'Payment attempt failed for [transaction_no]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Unfortunately, we couldn&#039;t complete your payment of [payment_amount] [payment_currency] via [payment_method].</p>
<p>Transaction: [transaction_no]</p>
<p>Reason provided by the processor: [failure_reason]</p>
<p>The attempt was logged at [payment_created_at]. Please try again or contact us at [support_email] if you need assistance.</p>
<p>- The [app_name] Billing Team</p>
HTML,
        ],
        'PAYMENT_REFUNDED' => [
            'name' => 'Payment Refunded',
            'category' => 'finance',
            'description' => 'Notifies users that a payment has been refunded.',
            'subject' => 'A refund was issued for [transaction_no]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>We processed a refund of [refund_amount] [refund_currency] for transaction [transaction_no].</p>
<p>Reason: [refund_reason]</p>
<p>The refund was completed on [refund_processed_at]. Funds may take a few days to reach your account.</p>
<p>Need help? Contact us at [support_email].</p>
<p>Regards,<br>The [app_name] Billing Team</p>
HTML,
        ],
        'TRANSACTION_CREATED' => [
            'name' => 'Transaction Posted',
            'category' => 'finance',
            'description' => 'Provides details when a ledger transaction is recorded.',
            'subject' => 'New transaction on your account ([trx])',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>A new [trx_type] transaction was posted to your account.</p>
<ul>
    <li>Reference: [trx]</li>
    <li>Amount: [amount] [currency]</li>
    <li>Balance before: [balance_before]</li>
    <li>Balance after: [balance_after]</li>
    <li>Details: [transaction_details]</li>
    <li>Date: [transaction_date]</li>
</ul>
<p>Review your activity whenever you like at [app_url].</p>
<p>Regards,<br>The [app_name] Team</p>
HTML,
        ],
        'SUPPORT_TICKET_CREATED' => [
            'name' => 'Support Ticket Created',
            'category' => 'support',
            'description' => 'Confirms receipt of a user support ticket.',
            'subject' => 'We created ticket #[ticket_id] for you',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Your support ticket #[ticket_id] &quot;[ticket_subject]&quot; has been opened with priority [ticket_priority].</p>
<p>You can view or update the ticket anytime at <a href="[ticket_url]">[ticket_url]</a>.</p>
<p>Our team will get back to you shortly.</p>
<p>Thanks,<br>The [app_name] Support Team</p>
HTML,
        ],
        'SUPPORT_TICKET_REPLY' => [
            'name' => 'Support Ticket Reply',
            'category' => 'support',
            'description' => 'Sends the contents of a newly posted reply on a ticket.',
            'subject' => 'You have a new reply on ticket #[ticket_id]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>[reply_author] replied to ticket #[ticket_id]:</p>
<blockquote>[reply_message]</blockquote>
<p>Continue the conversation at <a href="[ticket_url]">[ticket_url]</a>.</p>
<p>Regards,<br>The [app_name] Support Team</p>
HTML,
        ],
        'SUPPORT_TICKET_STATUS_CHANGED' => [
            'name' => 'Support Ticket Status Updated',
            'category' => 'support',
            'description' => 'Informs users when ticket state changes (answered, closed, etc.).',
            'subject' => 'Ticket #[ticket_id] is now [ticket_status]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Ticket #[ticket_id] has been updated to <strong>[ticket_status]</strong>.</p>
<p>You can review the details or reopen the ticket at <a href="[ticket_url]">[ticket_url]</a>.</p>
<p>Thank you,<br>The [app_name] Support Team</p>
HTML,
        ],
        'USER_LOGIN_ALERT' => [
            'name' => 'User Login Alert',
            'category' => 'security',
            'description' => 'Security notice about a login event for the user account.',
            'subject' => 'New login to your [app_name] account',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>Your account was just accessed.</p>
<ul>
    <li>Time: [login_time]</li>
    <li>IP: [login_ip]</li>
    <li>Device: [login_device]</li>
    <li>Location: [login_location]</li>
</ul>
<p>If this was you, no action is needed. If not, please secure your account immediately.</p>
<p>- The [app_name] Security Team</p>
HTML,
        ],
        'ADMIN_PASSWORD_RESET_REQUEST' => [
            'name' => 'Admin Password Reset Request',
            'category' => 'admin',
            'description' => 'Sent to administrators requesting a password reset.',
            'subject' => 'Reset your administrator password',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [admin_name],</p>
<p>We received a password reset request for your administrator account.</p>
<p><a href="[reset_url]" style="display:inline-block;padding:12px 20px;background:#0d6efd;color:#ffffff;border-radius:4px;text-decoration:none;">Reset Password</a></p>
<p>If you didn&#039;t request this, please ignore this message. Request origin IP: [request_ip]</p>
<p>Regards,<br>The [app_name] Team</p>
HTML,
        ],
        'ADMIN_PROFILE_UPDATED' => [
            'name' => 'Admin Profile Updated',
            'category' => 'admin',
            'description' => 'Confirms that an administrator profile was updated.',
            'subject' => 'Your admin profile was updated',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [admin_name],</p>
<p>Your administrator profile was updated on [updated_at]. Changes: [updated_fields]</p>
<p>You can review the details at <a href="[profile_url]">[profile_url]</a>.</p>
<p>If something looks wrong, contact another administrator immediately.</p>
<p>- The [app_name] Team</p>
HTML,
        ],
        'ADMIN_NEW_USER_REGISTERED' => [
            'name' => 'Admin Alert: New User',
            'category' => 'admin',
            'description' => 'Alerts administrators when a new user registers.',
            'subject' => 'A new user registered: [user_name]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello,</p>
<p>A new user just registered on [app_name].</p>
<ul>
    <li>Name: [user_name]</li>
    <li>Email: [user_email]</li>
    <li>Registered at: [user_registered_at]</li>
</ul>
<p>View the profile: <a href="[user_profile_url]">[user_profile_url]</a></p>
<p>- The [app_name] System</p>
HTML,
        ],
        'ADMIN_KYC_SUBMISSION' => [
            'name' => 'Admin Alert: KYC Submission',
            'category' => 'admin',
            'description' => 'Alerts administrators when a user submits KYC documents.',
            'subject' => 'KYC submission received from [user_name]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello,</p>
<p>[user_name] submitted new KYC documents.</p>
<ul>
    <li>Status: [kyc_status]</li>
    <li>Submitted at: [kyc_submitted_at]</li>
</ul>
<p>Review the submission: <a href="[kyc_review_url]">[kyc_review_url]</a></p>
<p>- The [app_name] System</p>
HTML,
        ],
        'ADMIN_PAYMENT_ALERT' => [
            'name' => 'Admin Alert: Payment Event',
            'category' => 'admin',
            'description' => 'Notifies administrators about new or updated payments.',
            'subject' => '[payment_status]: payment #[transaction_no]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello,</p>
<p>A payment event occurred.</p>
<ul>
    <li>User: [user_name]</li>
    <li>Amount: [payment_amount] [payment_currency]</li>
    <li>Method: [payment_method]</li>
    <li>Status: [payment_status]</li>
    <li>Transaction: [transaction_no]</li>
    <li>Created at: [payment_created_at]</li>
</ul>
<p>Inspect payment: <a href="[payment_admin_url]">[payment_admin_url]</a></p>
<p>- The [app_name] System</p>
HTML,
        ],
        'BROADCAST_NOTIFICATION' => [
            'name' => 'Broadcast Notification',
            'category' => 'communication',
            'description' => 'Generic template for broadcasting announcements to users.',
            'subject' => '[subject]',
            'view' => 'global',
            'body' => <<<'HTML'
<p>Hello [user_name],</p>
<p>[message]</p>
<p>Sent from [app_name] &bull; Need help? Contact [support_email].</p>
<p>Regards,<br>The [app_name] Team</p>
HTML,
        ],
    ],
];
