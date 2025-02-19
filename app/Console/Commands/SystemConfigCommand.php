<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemConfig;

class SystemConfigCommand extends Command
{
    protected $signature = 'system:config {action} {key?} {value?}';
    protected $description = 'Manage system configurations';

    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                $this->listConfigs();
                break;
            case 'get':
                $this->getConfig();
                break;
            case 'set':
                $this->setConfig();
                break;
        }
    }

    protected function listConfigs()
    {
        $configs = SystemConfig::all();
        $this->table(
            ['Key', 'Value', 'Group', 'Type'],
            $configs->map(fn($c) => [$c->key, $c->value, $c->group, $c->type])
        );
    }

    protected function getConfig()
    {
        $key = $this->argument('key');
        $value = SystemConfig::get($key);
        $this->info("$key: $value");
    }

    protected function setConfig()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');
        SystemConfig::set($key, $value);
        $this->info("Updated $key = $value");
    }
} 