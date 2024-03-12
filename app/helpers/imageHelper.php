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
    if(Storage::exists($oldPath)){
        Storage::delete($oldPath);
    }

    return uploadImage($image, $path);
}
