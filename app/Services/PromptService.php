<?php

namespace App\Services;

use App\Models\PromptTemplate;

class PromptService
{
    public function compilePrompt(PromptTemplate $template, array $params = [])
    {
        return $template->compile($params);
    }

    public function validateParameters(PromptTemplate $template, array $params)
    {
        $required = collect($template->parameters)->where('required', true)->pluck('name');
        $missing = $required->diff(array_keys($params));
        
        if ($missing->isNotEmpty()) {
            throw new \Exception('Missing required parameters: ' . $missing->implode(', '));
        }

        return true;
    }
} 