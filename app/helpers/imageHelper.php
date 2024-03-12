<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function uploadImage(UploadedFile $image, $path): string
{
    $extension = $image->getClientOriginalExtension();
    $imageName = 'media_'.uniqid().'.'.$extension;
    $image->storeAs($path, $imageName);

    return $path.$imageName;
}

function updateImage(UploadedFile $image, $oldPath, $path): string
{
    deleteImage($oldPath);

    return uploadImage($image, $path);
}

function deleteImage($path): void
{
    if (Storage::exists($path)) {
        Storage::delete($path);
    }
}
