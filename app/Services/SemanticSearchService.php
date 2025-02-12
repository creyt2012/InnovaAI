<?php

namespace App\Services;

use OpenAI\Client;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Cache;

class SemanticSearchService
{
    protected $client;
    protected $embeddingModel = 'text-embedding-3-small';

    public function __construct()
    {
        $this->client = new Client(config('services.openai.api_key'));
    }

    public function search($query, $limit = 5)
    {
        $queryEmbedding = $this->getEmbedding($query);
        
        return KnowledgeBase::search()
            ->whereRaw("embedding_vector <=> ?", [$queryEmbedding])
            ->take($limit)
            ->get();
    }

    public function getEmbedding($text)
    {
        $cacheKey = 'embedding:' . md5($text);
        
        return Cache::remember($cacheKey, 86400, function() use ($text) {
            $response = $this->client->embeddings()->create([
                'model' => $this->embeddingModel,
                'input' => $text
            ]);
            
            return $response['data'][0]['embedding'];
        });
    }

    public function updateEmbeddings()
    {
        KnowledgeBase::chunk(100, function($articles) {
            foreach ($articles as $article) {
                $embedding = $this->getEmbedding($article->content);
                $article->update(['embedding_vector' => $embedding]);
            }
        });
    }
} 