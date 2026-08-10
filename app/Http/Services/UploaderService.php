<?php

namespace App\Http\Services;

class UploaderService
{
    /**
     * Upload files
     */
    public function upload($file, $roomCode, $folder)
    {
        $datePath = now()->format('Y/F/d');

        $folder = "rooms/{$roomCode}/{$folder}/{$datePath}";

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }
}
