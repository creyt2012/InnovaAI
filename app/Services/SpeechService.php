<?php

namespace App\Services;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Illuminate\Support\Facades\Storage;

class SpeechService
{
    protected $sttClient;
    protected $ttsClient;

    public function __construct()
    {
        $this->sttClient = new SpeechClient([
            'credentials' => storage_path('app/google-credentials.json')
        ]);
        $this->ttsClient = new TextToSpeechClient([
            'credentials' => storage_path('app/google-credentials.json')
        ]);
    }

    public function convertSpeechToText($audioContent)
    {
        $audio = ['content' => $audioContent];
        $config = [
            'language_code' => 'vi-VN',
            'encoding' => 'LINEAR16',
            'sample_rate_hertz' => 16000,
        ];

        $response = $this->sttClient->recognize($config, $audio);
        foreach ($response->getResults() as $result) {
            return $result->getAlternatives()[0]->getTranscript();
        }
    }

    public function convertTextToSpeech($text)
    {
        $input = ['text' => $text];
        $voice = [
            'language_code' => 'vi-VN',
            'name' => 'vi-VN-Standard-A'
        ];
        $audioConfig = [
            'audio_encoding' => 'MP3'
        ];

        $response = $this->ttsClient->synthesizeSpeech($input, $voice, $audioConfig);
        $audioContent = $response->getAudioContent();

        $filename = 'speech-' . time() . '.mp3';
        Storage::put('public/audio/' . $filename, $audioContent);

        return Storage::url('audio/' . $filename);
    }
} 