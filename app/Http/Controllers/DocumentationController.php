<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class DocumentationController extends Controller
{
    protected $docsPath;
    protected $converter;

    public function __construct()
    {
        $this->docsPath = resource_path('docs');
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function index()
    {
        $sections = [
            'user' => 'User Guide',
            'developer' => 'Developer Guide',
            'admin' => 'Admin Guide',
            'api' => 'API Documentation',
            'plugins' => 'Plugin Development'
        ];

        return view('documentation.index', compact('sections'));
    }

    public function show($section, $page = 'index')
    {
        $filePath = "{$this->docsPath}/{$section}/{$page}.md";
        
        if (!File::exists($filePath)) {
            abort(404);
        }

        $content = File::get($filePath);
        $html = $this->converter->convert($content);
        
        // Get sidebar navigation
        $navigation = $this->getNavigation($section);

        return view('documentation.show', [
            'content' => $html,
            'navigation' => $navigation,
            'currentSection' => $section,
            'currentPage' => $page
        ]);
    }

    protected function getNavigation($section)
    {
        $navFile = "{$this->docsPath}/{$section}/navigation.json";
        if (File::exists($navFile)) {
            return json_decode(File::get($navFile), true);
        }
        return [];
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $results = [];

        // Search through all markdown files
        $files = File::allFiles($this->docsPath);
        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $content = File::get($file->getPathname());
                if (stripos($content, $query) !== false) {
                    $results[] = [
                        'title' => $this->getDocTitle($content),
                        'path' => $this->getDocPath($file),
                        'excerpt' => $this->getSearchExcerpt($content, $query)
                    ];
                }
            }
        }

        return response()->json($results);
    }

    protected function getDocTitle($content)
    {
        if (preg_match('/^#\s*(.+)$/m', $content, $matches)) {
            return $matches[1];
        }
        return 'Untitled';
    }

    protected function getSearchExcerpt($content, $query)
    {
        $position = stripos($content, $query);
        $start = max(0, $position - 50);
        $excerpt = substr($content, $start, 100);
        return $start > 0 ? "...{$excerpt}..." : "{$excerpt}...";
    }
} 