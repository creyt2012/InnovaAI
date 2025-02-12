<?php

namespace App\Services;

use App\Models\VisitorAnalytic;
use App\Models\CustomEvent;
use Illuminate\Support\Facades\DB;

class DataRetentionService
{
    public function cleanupOldData()
    {
        $retentionDays = config('analytics.privacy.data_retention');

        DB::transaction(function () use ($retentionDays) {
            // Clean up visitor analytics
            VisitorAnalytic::where('created_at', '<', now()->subDays($retentionDays))->delete();

            // Clean up custom events
            CustomEvent::where('created_at', '<', now()->subDays($retentionDays))->delete();

            // Clean up other analytics data
        });
    }

    public function anonymizeData()
    {
        VisitorAnalytic::where('anonymized', false)
            ->update([
                'ip_address' => DB::raw('MD5(ip_address)'),
                'user_agent' => null,
                'anonymized' => true
            ]);
    }
} 