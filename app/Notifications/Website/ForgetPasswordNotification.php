<?php

namespace App\Notifications\Website;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 15;
    public $backoff = [2, 10, 20];

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'mail-queue',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->priority(1)
            ->subject('Reset Password Request for ' . config('app.name'))
            ->markdown('emails.website.forget-password', [
                'username' => $notifiable->name,
                'reset_token' => $notifiable->reset_password_token
            ]);
    }
}
