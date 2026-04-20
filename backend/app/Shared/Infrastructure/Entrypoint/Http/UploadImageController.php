<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Entrypoint\Http;

use App\Shared\Application\UploadImage\UploadImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadImageController
{
    public function __construct(private UploadImage $useCase) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $url = ($this->useCase)($request->file('image'));

        return new JsonResponse(['url' => $url]);
    }
}
