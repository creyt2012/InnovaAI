<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Services\KnowledgeBaseService;
use Illuminate\Http\Request;
use App\Jobs\TrainModelJob;

class KnowledgeBaseController extends Controller
{
    protected $knowledgeBaseService;

    public function __construct(KnowledgeBaseService $knowledgeBaseService)
    {
        $this->knowledgeBaseService = $knowledgeBaseService;
    }

    public function index()
    {
        $knowledgeBases = KnowledgeBase::with(['category', 'author'])
            ->latest()
            ->paginate(10);

        return view('admin.knowledge.index', compact('knowledgeBases'));
    }

    public function create()
    {
        return view('admin.knowledge.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source_type' => 'required|string|in:file,url,manual',
            'source_url' => 'nullable|url',
            'metadata' => 'nullable|array'
        ]);

        $knowledgeBase = KnowledgeBase::create([
            ...$validated,
            'author_id' => auth()->id()
        ]);

        return redirect()
            ->route('admin.knowledge.index')
            ->with('success', 'Knowledge base created successfully');
    }

    public function edit(KnowledgeBase $knowledgeBase)
    {
        return view('admin.knowledge.edit', compact('knowledgeBase'));
    }

    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'source_type' => 'required|string|in:file,url,manual',
            'source_url' => 'nullable|url',
            'metadata' => 'nullable|array'
        ]);

        $knowledgeBase->update($validated);

        return redirect()
            ->route('admin.knowledge.index')
            ->with('success', 'Knowledge base updated successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240'
        ]);

        try {
            $knowledgeBase = $this->knowledgeBaseService->importFromFile(
                $request->file('file')
            );

            return response()->json([
                'message' => 'File imported successfully',
                'knowledge_base' => $knowledgeBase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Import failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function train(KnowledgeBase $knowledgeBase)
    {
        try {
            TrainModelJob::dispatch($knowledgeBase);

            return response()->json([
                'message' => 'Training job queued successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to queue training job: ' . $e->getMessage()
            ], 422);
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $results = $this->knowledgeBaseService->search($query);

        return response()->json($results);
    }

    public function delete(KnowledgeBase $knowledgeBase)
    {
        try {
            $knowledgeBase->delete();

            return redirect()
                ->route('admin.knowledge.index')
                ->with('success', 'Knowledge base deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.knowledge.index')
                ->with('error', 'Failed to delete knowledge base: ' . $e->getMessage());
        }
    }

    public function export(KnowledgeBase $knowledgeBase)
    {
        try {
            $exportData = $this->knowledgeBaseService->export($knowledgeBase);

            return response()->json($exportData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function trainStatus(KnowledgeBase $knowledgeBase)
    {
        return response()->json([
            'status' => $knowledgeBase->training_status,
            'last_trained_at' => $knowledgeBase->last_trained_at,
            'metrics' => $knowledgeBase->training_metrics
        ]);
    }
} 