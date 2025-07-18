<?php
use App\Http\Controllers\API\AuthTokenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::post('/login-token', [AuthTokenController::class, 'login']);
Route::post('/logout-token', [AuthTokenController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
});