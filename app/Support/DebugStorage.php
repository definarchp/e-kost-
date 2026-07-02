<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class DebugStorage
{
    public static function publicUrlAndExists(?string $path): array
    {
        if (! $path) {
            return ['path' => null, 'url' => null, 'exists' => false];
        }

        $disk = Storage::disk('public');

        return [
            'path' => $path,
            'url' => $disk->url($path),
            'exists' => $disk->exists($path),
        ];
    }
}

