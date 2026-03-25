<?php

use App\User\Infrastructure\Entrypoint\Http\PostController;
use Illuminate\Support\Facades\Route;
use App\Tax\Infrastructure\Entrypoint\Http\GetAllTaxesController;
use App\Tax\Infrastructure\Entrypoint\Http\CreateTaxController;
use App\Tax\Infrastructure\Entrypoint\Http\UpdateTaxController;
use App\Tax\Infrastructure\Entrypoint\Http\DeleteTaxController;

Route::post('/users', PostController::class);
Route::get('/taxes', GetAllTaxesController::class);
Route::post('/taxes', CreateTaxController::class);
Route::put('/taxes/{uuid}', UpdateTaxController::class);
Route::delete('/taxes/{uuid}', DeleteTaxController::class);