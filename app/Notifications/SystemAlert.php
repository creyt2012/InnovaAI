<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SystemAlert extends Notification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('System Alert')
            ->line('There is an important system alert.');
    }
} 