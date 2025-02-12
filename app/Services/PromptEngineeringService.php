<?php

namespace App\Services;

class PromptEngineeringService
{
    public function testPrompt($prompt, $variables = [])
    {
        // Test prompt với nhiều biến số khác nhau
        $results = [];
        
        foreach ($variables as $var) {
            $formattedPrompt = $this->formatPrompt($prompt, $var);
            $response = $this->generateResponse($formattedPrompt);
            $results[] = [
                'input' => $var,
                'prompt' => $formattedPrompt,
                'response' => $response
            ];
        }
        
        return $results;
    }

    public function analyzePromptPerformance($promptId)
    {
        // Phân tích hiệu suất của prompt
        return [
            'success_rate' => 0.95,
            'average_tokens' => 150,
            'cost_per_request' => 0.002,
            'common_issues' => [
                'Prompt quá dài',
                'Thiếu context cụ thể'
            ]
        ];
    }

    public function suggestImprovements($prompt)
    {
        // Đề xuất cải thiện prompt
        return [
            'suggestions' => [
                'Thêm ví dụ cụ thể',
                'Rút gọn prompt',
                'Thêm ràng buộc output'
            ],
            'examples' => [
                'Before' => $prompt,
                'After' => 'Improved prompt version'
            ]
        ];
    }
} 