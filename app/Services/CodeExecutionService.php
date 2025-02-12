<?php

namespace App\Services;

use Docker\Docker;
use Docker\API\Model\ContainerConfig;

class CodeExecutionService
{
    protected $docker;
    protected $supportedLanguages = [
        'python' => 'python:3.9-slim',
        'node' => 'node:16-alpine',
        'php' => 'php:8.1-cli'
    ];

    public function __construct()
    {
        $this->docker = Docker::create();
    }

    public function execute($code, $language, $timeout = 30)
    {
        if (!isset($this->supportedLanguages[$language])) {
            throw new \Exception('Unsupported language');
        }

        $containerConfig = new ContainerConfig();
        $containerConfig->setImage($this->supportedLanguages[$language]);
        $containerConfig->setCmd($this->buildCommand($code, $language));
        $containerConfig->setTty(true);
        $containerConfig->setNetworkDisabled(true);

        $container = $this->docker->containerCreate($containerConfig);
        $this->docker->containerStart($container->getId());

        $output = $this->docker->containerLogs($container->getId(), [
            'stdout' => true,
            'stderr' => true
        ]);

        // Cleanup
        $this->docker->containerStop($container->getId());
        $this->docker->containerDelete($container->getId());

        return [
            'output' => $output,
            'execution_time' => $this->getExecutionTime($container->getId())
        ];
    }

    protected function buildCommand($code, $language)
    {
        return match($language) {
            'python' => ['python', '-c', $code],
            'node' => ['node', '-e', $code],
            'php' => ['php', '-r', $code]
        };
    }

    protected function getExecutionTime($containerId)
    {
        $stats = $this->docker->containerStats($containerId);
        return $stats['cpu_stats']['cpu_usage']['total_usage'];
    }
} 