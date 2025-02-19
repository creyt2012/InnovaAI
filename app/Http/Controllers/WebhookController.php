<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebhookService;

class WebhookController extends Controller
{
    protected $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('X-Webhook-Signature');

        if (!$this->webhookService->verifySignature($payload, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $this->webhookService->process($payload);

        return response()->json(['status' => 'success']);
    }
} 