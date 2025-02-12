<?php

namespace App\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class ImageAnalysisService
{
    protected $client;

    public function __construct()
    {
        $this->client = new ImageAnnotatorClient();
    }

    public function analyzeImage($imagePath)
    {
        $image = file_get_contents($imagePath);

        return [
            'text' => $this->detectText($image),
            'objects' => $this->detectObjects($image),
            'faces' => $this->detectFaces($image),
            'labels' => $this->detectLabels($image),
            'properties' => $this->detectProperties($image)
        ];
    }

    public function detectObjects($image)
    {
        $objects = $this->client->objectLocalization($image);
        return collect($objects)->map(function($object) {
            return [
                'name' => $object->getName(),
                'confidence' => $object->getScore(),
                'bounds' => $this->getBoundingBox($object)
            ];
        });
    }

    public function detectFaces($image)
    {
        $faces = $this->client->faceDetection($image);
        return collect($faces)->map(function($face) {
            return [
                'joy' => $face->getJoyLikelihood(),
                'anger' => $face->getAngerLikelihood(),
                'bounds' => $this->getBoundingBox($face)
            ];
        });
    }

    protected function getBoundingBox($object)
    {
        $bounds = $object->getBoundingPoly();
        return collect($bounds->getVertices())->map(function($vertex) {
            return ['x' => $vertex->getX(), 'y' => $vertex->getY()];
        });
    }
} 