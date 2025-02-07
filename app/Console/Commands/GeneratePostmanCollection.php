<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePostmanCollection extends Command
{
    protected $signature = 'api:postman';
    protected $description = 'Generate Postman collection from API documentation';

    public function handle()
    {
        $this->info('Generating Postman collection...');

        // Generate Scribe documentation first
        $this->call('scribe:generate');

        // Read the generated OpenAPI spec
        $openApiPath = public_path('docs/openapi.yaml');
        if (!File::exists($openApiPath)) {
            $this->error('OpenAPI spec not found!');
            return 1;
        }

        // Convert to Postman collection
        $collection = $this->convertToPostman($openApiPath);

        // Save collection
        $outputPath = storage_path('app/postman/LMStudio.postman_collection.json');
        File::put($outputPath, json_encode($collection, JSON_PRETTY_PRINT));

        $this->info('Postman collection generated at: ' . $outputPath);
        return 0;
    }

    protected function convertToPostman($openApiPath)
    {
        // Implement conversion logic here
        // You can use libraries like swagger2-postman-generator
        // or implement your own conversion
    }
} 