<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\UpdateTable\UpdateTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateTableController
{
    public function __construct(
        private UpdateTable $useCase,
    ) {}

    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $name = $request->input('name');
        $zoneId = $request->input('zone_id');

        $table = ($this->useCase)($uuid, $name, $zoneId);

        return new JsonResponse($table);
    }
}