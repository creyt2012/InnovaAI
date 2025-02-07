<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear cache before each test
        Cache::flush();
        Redis::flushall();
        
        // Disable model events during testing
        $this->withoutModelEvents();
        
        // Mock LMStudio service for testing
        $this->mockLMStudioService();
    }

    protected function mockLMStudioService()
    {
        $this->mock(\App\Services\LMStudioService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')
                 ->andReturn('Test response');
        });
    }

    protected function tearDown(): void
    {
        $this->afterApplicationCreated(function () {
            // Clean up after tests
        });

        parent::tearDown();
    }
} 