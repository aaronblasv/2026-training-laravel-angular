<?php

declare(strict_types=1);

namespace App\Shared\Application\UploadImage;

use App\Shared\Domain\Interfaces\ImageUploaderInterface;
use Illuminate\Http\UploadedFile;

class UploadImage
{
    public function __construct(private ImageUploaderInterface $imageUploader) {}

    public function __invoke(UploadedFile $file): string
    {
        return $this->imageUploader->upload($file);
    }
}