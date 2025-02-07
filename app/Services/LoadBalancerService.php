<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoadBalancerService
{
    protected $servers;
    protected $activeServers;
    protected $strategy = 'round-robin'; // round-robin, least-connections, weighted

    public function __construct()
    {
        $this->refreshServerList();
    }

    public function refreshServerList()
    {
        $this->servers = Server::where('status', 'active')->get();
        $this->activeServers = $this->servers->filter(function ($server) {
            return $this->isServerHealthy($server);
        });
    }

    public function processQuery(string $query, array $context = [])
    {
        // Phân tích và chia nhỏ query
        $chunks = $this->splitQuery($query);
        
        // Phân phối các chunks cho các server
        $results = collect($chunks)->map(function ($chunk) use ($context) {
            $server = $this->getNextServer();
            return $this->processChunk($server, $chunk, $context);
        });

        // Kết hợp kết quả
        return $this->combineResults($results);
    }

    protected function splitQuery(string $query): array
    {
        // Phân tích độ phức tạp của query
        $complexity = $this->analyzeComplexity($query);
        
        if ($complexity < 100) {
            return [$query]; // Không cần chia nhỏ
        }

        // Chia query thành các chunks nhỏ hơn
        $chunks = [];
        $sentences = $this->splitIntoSentences($query);
        
        $currentChunk = '';
        foreach ($sentences as $sentence) {
            if ($this->analyzeComplexity($currentChunk . $sentence) > 100) {
                if (!empty($currentChunk)) {
                    $chunks[] = $currentChunk;
                }
                $currentChunk = $sentence;
            } else {
                $currentChunk .= ' ' . $sentence;
            }
        }
        
        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    protected function analyzeComplexity(string $text): int
    {
        // Đánh giá độ phức tạp dựa trên:
        $wordCount = str_word_count($text);
        $charCount = strlen($text);
        $sentenceCount = substr_count($text, '.') + substr_count($text, '!') + substr_count($text, '?');
        
        // Tính điểm phức tạp
        $complexity = ($wordCount * 0.5) + ($charCount * 0.1) + ($sentenceCount * 10);
        
        return (int) $complexity;
    }

    protected function getNextServer()
    {
        switch ($this->strategy) {
            case 'round-robin':
                return $this->getRoundRobinServer();
            
            case 'least-connections':
                return $this->getLeastConnectionsServer();
            
            case 'weighted':
                return $this->getWeightedServer();
            
            default:
                return $this->getRoundRobinServer();
        }
    }

    protected function getRoundRobinServer()
    {
        $currentIndex = Cache::get('lb_current_index', 0);
        $server = $this->activeServers->get($currentIndex);
        
        $nextIndex = ($currentIndex + 1) % $this->activeServers->count();
        Cache::put('lb_current_index', $nextIndex, now()->addMinutes(5));
        
        return $server;
    }

    protected function getLeastConnectionsServer()
    {
        return $this->activeServers
            ->sortBy('active_connections')
            ->first();
    }

    protected function getWeightedServer()
    {
        return $this->activeServers
            ->sortByDesc(function ($server) {
                $weight = 100;
                
                // Giảm weight nếu server đang tải cao
                if ($server->metrics->cpu_usage > 80) $weight -= 30;
                if ($server->metrics->memory_usage > 80) $weight -= 30;
                if ($server->metrics->active_connections > 100) $weight -= 20;
                
                return $weight;
            })
            ->first();
    }

    protected function processChunk(Server $server, string $chunk, array $context)
    {
        try {
            $response = $this->sendRequest($server, [
                'chunk' => $chunk,
                'context' => $context
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'result' => $response->json('result'),
                    'server_id' => $server->id
                ];
            }

            Log::warning('Server processing failed', [
                'server_id' => $server->id,
                'chunk' => $chunk,
                'response' => $response->body()
            ]);

            // Thử lại với server khác
            $backupServer = $this->getBackupServer($server);
            if ($backupServer) {
                return $this->processChunk($backupServer, $chunk, $context);
            }

            return [
                'success' => false,
                'error' => 'Processing failed'
            ];

        } catch (\Exception $e) {
            Log::error('Chunk processing error', [
                'server_id' => $server->id,
                'chunk' => $chunk,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function sendRequest(Server $server, array $data)
    {
        return Http::timeout(30)
            ->withHeaders([
                'X-Request-ID' => (string) Str::uuid(),
            ])
            ->post("{$server->host}:{$server->port}/v1/process", $data);
    }

    protected function combineResults(Collection $results)
    {
        // Kiểm tra lỗi
        $errors = $results->where('success', false);
        if ($errors->isNotEmpty()) {
            Log::error('Some chunks failed processing', [
                'errors' => $errors->pluck('error')
            ]);
        }

        // Kết hợp kết quả thành công
        $successfulResults = $results->where('success', true)
            ->pluck('result')
            ->filter();

        // Ghép nối các kết quả
        return $this->mergeResponses($successfulResults);
    }

    protected function mergeResponses(Collection $responses)
    {
        return $responses->map(function ($response) {
                return trim($response);
            })
            ->filter()
            ->join("\n\n");
    }

    protected function isServerHealthy(Server $server): bool
    {
        $metrics = $server->latestMetrics;
        
        if (!$metrics) return false;
        
        // Kiểm tra các chỉ số sức khỏe
        if ($metrics->cpu_usage > 90) return false;
        if ($metrics->memory_usage > 90) return false;
        if ($metrics->response_time > 5000) return false; // >5s
        
        return true;
    }

    protected function getBackupServer(Server $excludeServer)
    {
        return $this->activeServers
            ->where('id', '!=', $excludeServer->id)
            ->sortBy(function ($server) {
                return $server->metrics->active_connections;
            })
            ->first();
    }

    protected function splitIntoSentences(string $text): array
    {
        // Tách câu thông minh hơn, tránh tách nhầm các viết tắt
        $abbreviations = ['Mr.', 'Mrs.', 'Dr.', 'Prof.', 'Sr.', 'Jr.', 'vs.', 'etc.'];
        
        // Thay thế tạm thời các viết tắt
        foreach ($abbreviations as $abbr) {
            $text = str_replace($abbr, str_replace('.', '{{DOT}}', $abbr), $text);
        }
        
        // Tách câu
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        
        // Khôi phục các viết tắt
        return array_map(function ($sentence) {
            return str_replace('{{DOT}}', '.', $sentence);
        }, $sentences);
    }
} 