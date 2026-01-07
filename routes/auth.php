<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\Auth\AuthController;

Route::middleware(CorsMiddleware::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});