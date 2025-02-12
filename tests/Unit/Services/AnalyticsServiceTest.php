<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\VisitorTrackingService;
use App\Models\VisitorAnalytic;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VisitorTrackingService();
    }

    /** @test */
    public function it_can_track_visitor()
    {
        $request = request()->merge([
            'ip' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ]);

        $visitor = $this->service->trackVisit($request);

        $this->assertInstanceOf(VisitorAnalytic::class, $visitor);
        $this->assertEquals('127.0.0.1', $visitor->ip_address);
    }

    /** @test */
    public function it_can_get_analytics_data()
    {
        // Create test data
        VisitorAnalytic::factory()->count(5)->create();

        $stats = $this->service->getAnalytics('today');

        $this->assertEquals(5, $stats['total_visitors']);
    }
} 