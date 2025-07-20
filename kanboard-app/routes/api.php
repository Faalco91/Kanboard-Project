<?php
use App\Http\Controllers\API\AuthTokenController;
use App\Http\Controllers\API\StatisticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::post('/login-token', [AuthTokenController::class, 'login']);
Route::post('/logout-token', [AuthTokenController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);
    
    // Routes pour les statistiques
    Route::get('/statistics/dashboard', [StatisticsController::class, 'dashboard']);
    Route::get('/statistics/projects/{project}', [StatisticsController::class, 'projectStatistics']);
});