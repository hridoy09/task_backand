<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $viewName;
    public $data;
    public $user;
    public $attachments;

    public function __construct(string $subjectLine, string $viewName, array $data, $user = null, array $attachments = [])
    {
        $this->subjectLine = $subjectLine;
        $this->viewName    = $viewName;
        $this->data        = $data;
        $this->user        = $user;
        $this->attachments = $attachments;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectLine)
            ->view('emails.' . $this->viewName)
            ->with(array_merge($this->data, ['user' => $this->user]));

        foreach ($this->attachments as $attachment) {
            if (is_array($attachment)) {
                $path = $attachment['path'] ?? null;
                $name = $attachment['name'] ?? null;

                if (!$path) {
                    continue;
                }

                if (file_exists($path)) {
                    $mail->attach($path, array_filter(['as' => $name]));
                    continue;
                }

                if (Storage::disk('local')->exists($path)) {
                    $mail->attach(Storage::disk('local')->path($path), array_filter(['as' => $name]));
                }

                continue;
            }

            if (is_string($attachment) && file_exists($attachment)) {
                $mail->attach($attachment);
            } elseif (is_string($attachment) && Storage::disk('local')->exists($attachment)) {
                $mail->attach(Storage::disk('local')->path($attachment));
            }
        }

        return $mail;
    }
}
use Illuminate\Support\Facades\Storage;
