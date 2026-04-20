<?php

declare(strict_types=1);

namespace App\Shared\Domain\Interfaces;

use Illuminate\Http\UploadedFile;

interface ImageUploaderInterface
{
    public function upload(UploadedFile $file): string;
}