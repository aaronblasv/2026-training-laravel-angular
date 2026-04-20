<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Services;

use App\Shared\Domain\Interfaces\ImageUploaderInterface;
use Illuminate\Http\UploadedFile;

class PublicStorageImageUploader implements ImageUploaderInterface
{
    public function upload(UploadedFile $file): string
    {
        $path = $file->store('images', 'public');

        return asset('storage/' . $path);
    }
}