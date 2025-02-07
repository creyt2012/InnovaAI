<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public function log($action, $model = null, $data = [])
    {
        $log = new AuditLog([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'data' => array_merge($data, [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
            ])
        ]);

        $log->save();

        return $log;
    }

    public function logLogin($success, $username)
    {
        $this->log($success ? 'login_success' : 'login_failed', null, [
            'username' => $username,
            'remember' => request()->has('remember')
        ]);
    }

    public function logApiAccess($endpoint, $response)
    {
        $this->log('api_access', null, [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'duration' => $response->handlerStats()['total_time'] ?? null
        ]);
    }

    public function logConfigChange($config, $oldValue, $newValue)
    {
        $this->log('config_change', null, [
            'config' => $config,
            'old_value' => $oldValue,
            'new_value' => $newValue
        ]);
    }
} 