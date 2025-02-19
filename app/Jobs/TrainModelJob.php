<?php

namespace App\Jobs;

use App\Models\KnowledgeBase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ModelTrainingService;
use App\Events\ModelTrainingCompleted;
use App\Events\ModelTrainingFailed;

class TrainModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $knowledgeBase;

    public function __construct(KnowledgeBase $knowledgeBase)
    {
        $this->knowledgeBase = $knowledgeBase;
    }

    public function handle(ModelTrainingService $trainingService)
    {
        try {
            $trainingService->trainModel($this->knowledgeBase);
            
            $this->knowledgeBase->update([
                'last_trained_at' => now()
            ]);
            
            // Gửi thông báo hoàn thành
            event(new ModelTrainingCompleted($this->knowledgeBase));
        } catch (\Exception $e) {
            // Log lỗi và gửi thông báo
            logger()->error('Model training failed: ' . $e->getMessage());
            event(new ModelTrainingFailed($this->knowledgeBase, $e->getMessage()));
        }
    }
} 