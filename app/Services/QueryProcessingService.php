<?php

namespace App\Services;

use App\Models\LmStudioApi;
use App\Models\Server;
use App\Models\QueryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\LoadBalancerService;

class QueryProcessingService
{
    protected $api;
    protected $loadBalancer;
    protected $systemPrompt = "You are a helpful AI assistant that provides accurate, informative responses.";

    public function __construct(LoadBalancerService $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
        $this->api = Cache::remember('active_lmstudio_api', 60, function () {
            return LmStudioApi::where('is_active', true)->first();
        });
    }

    public function processQuery(string $query, $user, $attachments = [])
    {
        if (!$this->api) {
            throw new \Exception('Không có API LM Studio nào đang hoạt động');
        }

        try {
            // Xử lý context từ file đính kèm
            $context = $this->processAttachments($attachments);
            
            // Tạo prompt hoàn chỉnh
            $fullPrompt = $this->buildPrompt($query, $context);

            // Sử dụng load balancer để xử lý
            $response = $this->loadBalancer->processQuery($fullPrompt, [
                'user_id' => $user->id,
                'api_key' => $this->api->api_key,
                'model' => $this->api->configuration['model'] ?? 'gpt-3.5-turbo',
                'system_prompt' => $this->systemPrompt
            ]);

            // Log response
            $this->logQuery($user, $query, $response);

            return $response;

        } catch (\Exception $e) {
            \Log::error('Query processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function processAttachments($attachments)
    {
        if (empty($attachments)) {
            return '';
        }

        $context = "Context from attached files:\n\n";
        foreach ($attachments as $attachment) {
            $content = $this->extractContent($attachment);
            if ($content) {
                $context .= "File {$attachment->original_filename}:\n{$content}\n\n";
            }
        }

        return $context;
    }

    protected function extractContent($attachment)
    {
        try {
            $path = storage_path('app/public/' . $attachment->path);
            $content = '';

            switch ($attachment->mime_type) {
                case 'text/plain':
                    $content = file_get_contents($path);
                    break;
                    
                case 'application/pdf':
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($path);
                    $content = $pdf->getText();
                    break;
                    
                case 'application/msword':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    $content = $this->extractTextFromWord($path);
                    break;
                    
                default:
                    return null;
            }

            // Giới hạn độ dài context
            return substr($content, 0, 2000) . (strlen($content) > 2000 ? '...' : '');
            
        } catch (\Exception $e) {
            \Log::error('File processing error: ' . $e->getMessage());
            return null;
        }
    }

    protected function buildPrompt($query, $context = '')
    {
        $prompt = '';
        
        if ($context) {
            $prompt .= $context . "\n\n";
        }
        
        $prompt .= "Question: " . $query . "\n\n";
        $prompt .= "Please provide a detailed and accurate response based on the given context and question.";
        
        return $prompt;
    }

    protected function logQuery($user, $query, $response)
    {
        QueryLog::create([
            'user_id' => $user->id,
            'query' => $query,
            'response' => $response['choices'][0]['message']['content'] ?? '',
            'metadata' => [
                'model' => $response['model'] ?? null,
                'tokens' => [
                    'prompt' => $response['usage']['prompt_tokens'] ?? 0,
                    'completion' => $response['usage']['completion_tokens'] ?? 0,
                    'total' => $response['usage']['total_tokens'] ?? 0
                ],
                'api_version' => $response['api_version'] ?? null
            ],
            'status' => 'completed'
        ]);
    }

    protected function streamResponse($response)
    {
        return response()->stream(function () use ($response) {
            foreach ($response->json() as $chunk) {
                echo "data: " . json_encode($chunk) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    private function extractTextFromWord($path)
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        $text = '';
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . ' ';
                }
            }
        }
        
        return $text;
    }
} 