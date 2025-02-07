<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index()
    {
        $models = AiModel::with('server')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                    'description' => $model->description,
                    'category' => $model->category,
                    'parameters' => $model->parameters,
                    'server' => $model->server->name,
                    'status' => $model->getStatus(),
                    'latency' => $model->getLatency()
                ];
            });

        return response()->json([
            'models' => $models
        ]);
    }

    public function status(AiModel $model)
    {
        return response()->json([
            'status' => $model->getStatus(),
            'latency' => $model->getLatency()
        ]);
    }
} 