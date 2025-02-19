<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaService
{
    public function uploadImage($file, $path = 'uploads')
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Tối ưu hóa hình ảnh
        $image = Image::make($file)
            ->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 80);
            
        Storage::put("public/{$path}/{$filename}", $image);
        
        return "storage/{$path}/{$filename}";
    }
} 