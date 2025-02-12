<?php

namespace App\Services;

use App\Models\Plugin;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class PluginMarketplaceService
{
    public function listAvailablePlugins()
    {
        return [
            'calculator' => [
                'name' => 'Calculator',
                'description' => 'Tính toán phức tạp',
                'version' => '1.0.0',
                'author' => 'LM Studio',
                'price' => 0,
                'status' => 'active',
                'icon' => 'fas fa-calculator',
                'permissions' => ['use_calculator'],
                'settings' => [
                    'decimal_places' => 2,
                    'enable_scientific' => true
                ]
            ],
            'translator' => [
                'name' => 'Translator',
                'description' => 'Dịch đa ngôn ngữ',
                'version' => '1.0.0',
                'author' => 'LM Studio',
                'price' => 0,
                'status' => 'active',
                'icon' => 'fas fa-language',
                'permissions' => ['use_translator'],
                'settings' => [
                    'default_source' => 'en',
                    'default_target' => 'vi'
                ]
            ],
            'code_interpreter' => [
                'name' => 'Code Interpreter',
                'description' => 'Thực thi và debug code',
                'version' => '1.0.0',
                'author' => 'LM Studio',
                'price' => 0,
                'status' => 'active',
                'icon' => 'fas fa-code',
                'permissions' => ['use_code_interpreter'],
                'settings' => [
                    'allowed_languages' => ['python', 'javascript', 'php'],
                    'timeout' => 30
                ]
            ]
        ];
    }

    public function installPlugin($pluginId)
    {
        $plugins = $this->listAvailablePlugins();
        
        if (!isset($plugins[$pluginId])) {
            throw new \Exception('Plugin not found');
        }

        $pluginData = $plugins[$pluginId];
        
        // Create plugin record
        $plugin = Plugin::create([
            'name' => $pluginData['name'],
            'slug' => $pluginId,
            'description' => $pluginData['description'],
            'version' => $pluginData['version'],
            'author' => $pluginData['author'],
            'price' => $pluginData['price'],
            'status' => $pluginData['status'],
            'settings' => $pluginData['settings'],
            'permissions' => $pluginData['permissions']
        ]);

        // Run migrations if any
        $migrationPath = base_path("plugins/{$pluginId}/migrations");
        if (file_exists($migrationPath)) {
            Artisan::call('migrate', [
                '--path' => "plugins/{$pluginId}/migrations",
                '--force' => true
            ]);
        }

        // Publish assets if any
        $assetsPath = base_path("plugins/{$pluginId}/assets");
        if (file_exists($assetsPath)) {
            Storage::disk('public')->makeDirectory("plugins/{$pluginId}");
            // Copy assets to public directory
        }

        return $plugin;
    }

    public function uninstallPlugin($pluginId)
    {
        $plugin = Plugin::where('slug', $pluginId)->firstOrFail();

        // Remove plugin data
        $plugin->delete();

        // Rollback migrations if any
        $migrationPath = base_path("plugins/{$pluginId}/migrations");
        if (file_exists($migrationPath)) {
            Artisan::call('migrate:rollback', [
                '--path' => "plugins/{$pluginId}/migrations",
                '--force' => true
            ]);
        }

        // Remove assets if any
        Storage::disk('public')->deleteDirectory("plugins/{$pluginId}");

        return true;
    }
} 