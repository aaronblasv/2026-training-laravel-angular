<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\GetAllTables\GetAllTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllTablesController
{
    public function __construct(
        private GetAllTables $useCase,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $tables = ($this->useCase)();

        return new JsonResponse($tables);
    }
}