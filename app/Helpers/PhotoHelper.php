<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class PhotoHelper
{
    public function downloadAndConvertToBase64(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return null;
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        $base64 = base64_encode($imageData);
        return "data:$mimeType;base64,$base64";
    }
}
