<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Spatie\PdfToText\Pdf;

class DocumentProcessingService
{
    protected $visionClient;

    public function __construct()
    {
        $this->visionClient = new ImageAnnotatorClient([
            'credentials' => storage_path('app/google-credentials.json')
        ]);
    }

    public function extractTextFromImage($imagePath)
    {
        $image = file_get_contents($imagePath);
        $response = $this->visionClient->textDetection($image);
        $texts = $response->getTextAnnotations();
        
        return $texts[0]->getDescription();
    }

    public function extractTextFromPdf($pdfPath)
    {
        return (new Pdf())
            ->setPdf($pdfPath)
            ->setLanguage('vie')
            ->text();
    }

    public function processDocument($file)
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->store('temp');
        $fullPath = storage_path('app/' . $path);

        $text = match($extension) {
            'pdf' => $this->extractTextFromPdf($fullPath),
            'jpg', 'jpeg', 'png' => $this->extractTextFromImage($fullPath),
            default => throw new \Exception('Unsupported file type')
        };

        // Cleanup
        unlink($fullPath);

        return $text;
    }
} 