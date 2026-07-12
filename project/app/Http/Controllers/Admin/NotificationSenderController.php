<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\UserNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationSenderController extends Controller
{
    use UserNotifier;

    public function index()
    {
        goIfUserCan('view-notifications.sender');

        $title = 'Notification Sender';

        $userGroups = $this->userGroups();

        return view('admin.notification.index', compact('title', 'userGroups'));
    }

    public function send(Request $request)
    {
        goIfUserCan('save-notifications.sender');

        $data = $request->validate([
            'emails'    => 'required|string',
            'subject'   => 'required|string|max:255',
            'mail_body' => 'required|string',
        ]);

        // Decode emails from JSON array input
        $raw = json_decode($data['emails'], true) ?: [];
        $emails = collect($raw)
            ->pluck('value')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($emails)) {
            return back()->withErrors(['emails' => 'No valid email addresses provided.']);
        }

        foreach ($emails as $email) {
            $guessedName = Str::title(str_replace(['.', '_', '-'], ' ', Str::before($email, '@')));
            $userName = trim($guessedName) ?: __('there');

            sendTemplatedNotification(
                $email,
                'BROADCAST_NOTIFICATION',
                [
                    'user_name' => $userName,
                    'subject' => $data['subject'],
                    'message' => $data['mail_body'],
                ]
            );
        }

        $success = true;

        if ($success) {
            return back()->with('success', 'Emails sent to: ' . implode(', ', $emails));
        } else {
            return back()->withErrors(['emails' => 'Failed to send emails. Check logs.']);
        }
    }
}
