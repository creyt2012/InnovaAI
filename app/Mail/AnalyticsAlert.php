<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnalyticsAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $data;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('emails.analytics.alert')
            ->subject("Analytics Alert: {$this->type}");
    }
} 