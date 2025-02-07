<?php

namespace App\Jobs;

use App\Services\QueryProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessQueryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $query;
    protected $user;

    public function __construct($query, $user)
    {
        $this->query = $query;
        $this->user = $user;
    }

    public function handle(QueryProcessingService $service)
    {
        return $service->processQuery($this->query, $this->user);
    }
} 