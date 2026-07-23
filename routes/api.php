<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\CorsMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

include __DIR__.'/auth.php';

Route::middleware(CorsMiddleware::class)->group(function () {
    Route::get('/data', [ApiController::class, 'getData']);
    Route::get('/sliders', [ApiController::class, 'getSliders']);
    Route::get('/about-us', [ApiController::class, 'getAboutUs']);
    Route::get('/initiatives', [ApiController::class, 'getInitiatives']);
    Route::get('/gallery', [ApiController::class, 'getGallery']);
    Route::get('/projects', [ApiController::class, 'getProjects']);
    Route::get('/portfolio', [App\Http\Controllers\ApiController::class, 'getPortfolio']);

    Route::middleware('auth:sanctum')->group(function () {
       

    });

        Route::post('/slider/create', [ApiController::class, 'createSlider']);
        Route::post('/slider/{id}/update', [ApiController::class, 'updateSlider']);
        Route::post('/slider/{id}/delete', [ApiController::class, 'deleteSlider']);
        Route::post('/about-us/create', [ApiController::class, 'createAboutUs']);
        Route::post('/about-us/update', [ApiController::class, 'updateAboutUs']);
        Route::post('/initiatives/create', [ApiController::class, 'createInitiative']);
        Route::post('/initiatives/{id}/update', [ApiController::class, 'updateInitiative']);
        Route::post('/gallery/create', [ApiController::class, 'createGallery']);
        Route::post('/gallery/{id}/update', [ApiController::class, 'updateGalleryItem']);
        Route::post('/projects/create', [ApiController::class, 'createProject']);
        Route::post('/projects/{id}/update', [ApiController::class, 'updateProject']);
        Route::post('/portfolio/create', [ApiController::class, 'createPortfolioItem']);
        Route::post('/portfolio/{id}/update', [ApiController::class, 'updatePortfolioItem']);


       

   
});

 //career routes
        Route::post('/career', [App\Http\Controllers\CareerController::class, 'store']);
        Route::get('/career', [App\Http\Controllers\CareerController::class, 'index']);
        Route::get('/career/{id}', [App\Http\Controllers\CareerController::class, 'show']);
        Route::put('/career/{id}', [App\Http\Controllers\CareerController::class, 'update']);
        Route::delete('/career/{id}', [App\Http\Controllers\CareerController::class, 'destroy']);

        //application routes
        Route::post('/career/submit', [App\Http\Controllers\ApplicationController::class, 'store']);
        Route::get('/applications', [App\Http\Controllers\ApplicationController::class, 'index']);
        Route::get('/applications/{id}', [App\Http\Controllers\ApplicationController::class, 'show']);
        Route::delete('/applications/{id}', [App\Http\Controllers\ApplicationController::class, 'destroy']);