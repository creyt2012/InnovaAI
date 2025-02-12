<?php

namespace App\Services;

use App\Models\ABTest;
use App\Models\ABTestParticipant;

class ABTestingService
{
    public function createTest($name, $variants, $targetMetric)
    {
        return ABTest::create([
            'name' => $name,
            'variants' => $variants,
            'target_metric' => $targetMetric,
            'start_date' => now()
        ]);
    }

    public function assignVariant($testId, $visitorId)
    {
        $test = ABTest::findOrFail($testId);
        $variants = collect($test->variants);
        
        // Assign variant using weighted random selection
        $variant = $variants->random();
        
        return ABTestParticipant::create([
            'test_id' => $testId,
            'visitor_id' => $visitorId,
            'variant' => $variant
        ]);
    }

    public function trackConversion($testId, $visitorId, $value = null)
    {
        return ABTestParticipant::where([
            'test_id' => $testId,
            'visitor_id' => $visitorId
        ])->update([
            'converted' => true,
            'conversion_value' => $value
        ]);
    }

    public function getResults($testId)
    {
        $test = ABTest::findOrFail($testId);
        $results = [];
        
        foreach ($test->variants as $variant) {
            $participants = ABTestParticipant::where([
                'test_id' => $testId,
                'variant' => $variant
            ]);
            
            $results[$variant] = [
                'total' => $participants->count(),
                'conversions' => $participants->where('converted', true)->count(),
                'conversion_rate' => $this->calculateConversionRate(
                    $participants->count(),
                    $participants->where('converted', true)->count()
                )
            ];
        }
        
        return $results;
    }

    protected function calculateConversionRate($total, $conversions)
    {
        if ($total === 0) return 0;
        return round(($conversions / $total) * 100, 2);
    }
} 