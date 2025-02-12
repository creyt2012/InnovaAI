<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledReport extends Mailable
{
    use Queueable, SerializesModels;

    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->markdown('emails.analytics.report')
            ->subject('Analytics Scheduled Report')
            ->attachFromStorage($this->report);
    }
} 