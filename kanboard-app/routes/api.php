<?php
use App\Http\Controllers\API\AuthTokenController;
use Illuminate\Support\Facades\Route;

Route::post('/login-token', [AuthTokenController::class, 'login']);
Route::post('/logout-token', [AuthTokenController::class, 'logout'])->middleware('auth:sanctum');