<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\LoadBalancerService;
use Illuminate\Support\Facades\Http;

class ServerController extends Controller
{
    protected $loadBalancer;

    public function __construct(LoadBalancerService $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    public function index()
    {
        $servers = Server::with('latestMetrics')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Servers/Index', [
            'servers' => $servers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'configuration' => 'nullable|json',
        ]);

        $server = Server::create($validated);

        return redirect()->route('admin.servers.index')
            ->with('message', 'Server created successfully');
    }

    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'configuration' => 'nullable|json',
        ]);

        $server->update($validated);

        return redirect()->route('admin.servers.index')
            ->with('message', 'Server updated successfully');
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('message', 'Server deleted successfully');
    }

    public function test(Server $server)
    {
        try {
            $response = Http::timeout(5)
                ->get("{$server->host}:{$server->port}/health");

            if ($response->successful()) {
                $server->update([
                    'status' => 'active',
                    'last_ping_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful'
                ]);
            }

            throw new \Exception('Health check failed');

        } catch (\Exception $e) {
            $server->update(['status' => 'error']);

            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 