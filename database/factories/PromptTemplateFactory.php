<?php

namespace Database\Factories;

use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromptTemplateFactory extends Factory
{
    protected $model = PromptTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph,
            'category' => $this->faker->randomElement(['general', 'support', 'marketing']),
            'parameters' => [],
            'is_active' => true
        ];
    }
} 