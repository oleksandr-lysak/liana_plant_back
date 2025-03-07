<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64Image implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return false;
        }

        // Remove the "data:image/{type};base64," prefix if it exists
        if (preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            $value = substr($value, strpos($value, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif, etc.
        } else {
            return false;
        }

        // Decode the Base64 string
        $decodedValue = base64_decode($value, true);

        if ($decodedValue === false) {
            return false;
        }

        // Validate the MIME type by checking the image type
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $decodedValue, FILEINFO_MIME_TYPE);
        finfo_close($f);

        $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mimeType, $validMimeTypes)) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid image.';
    }
}
