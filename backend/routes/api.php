<?php

use App\User\Infrastructure\Entrypoint\Http\PostController;
use Illuminate\Support\Facades\Route;
use App\Tax\Infrastructure\Entrypoint\Http\GetAllTaxesController;
use App\Tax\Infrastructure\Entrypoint\Http\CreateTaxController;

Route::post('/users', PostController::class);
Route::get('/taxes', GetAllTaxesController::class);
Route::post('/taxes', CreateTaxController::class);