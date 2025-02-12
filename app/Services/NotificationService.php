<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\AnalyticsAlert;
use App\Mail\ScheduledReport;

class NotificationService
{
    public function sendAlert($type, $data)
    {
        $recipients = $this->getAlertRecipients($type);
        
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new AnalyticsAlert($type, $data));
        }
    }

    public function sendScheduledReport($report, $recipients)
    {
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new ScheduledReport($report));
        }
    }

    protected function getAlertRecipients($type)
    {
        // Get recipients based on alert type
    }
} 