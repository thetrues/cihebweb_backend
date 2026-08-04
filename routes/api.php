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
        Route::put('/gallery/{id}', [ApiController::class, 'updateGallery']);


    
        Route::post('/portfolio/create', [ApiController::class, 'createPortfolioItem']);
        Route::post('/portfolio/{id}/update', [ApiController::class, 'updatePortfolioItem']);


       //admin/project
       Route::get('/admin/project', [ApiController::class, 'listProjects']);
        Route::get('/admin/projects/{id}', [ApiController::class, 'getProjectById']);
        Route::get('/admin/projects/{id}/view', [ApiController::class, 'viewProject']);
        Route::post('/admin/project', [ApiController::class, 'createProject']);
        Route::put('/admin/project/{id}', [ApiController::class, 'updateProject']);
        Route::delete('/admin/project/{id}', [ApiController::class, 'deleteProject']);


   
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


    // success stories routes
    Route::post('/success-story', [App\Http\Controllers\ApiController::class, 'createSuccessStory']);
    Route::get('/success-story', [App\Http\Controllers\ApiController::class, 'getSuccessStoriesAll']);
    Route::get('/success-stories', [App\Http\Controllers\ApiController::class, 'getSuccessStories']);
    Route::get('/success-stories/{id}', [App\Http\Controllers\ApiController::class, 'getSuccessStoryById']);
    Route::put('/success-story/{id}', [App\Http\Controllers\ApiController::class, 'updateSuccessStory']);
    Route::delete('/success-story/{id}', [App\Http\Controllers\ApiController::class, 'deleteSuccessStory']);

    // admin/program routes
    Route::get('admin/program', [App\Http\Controllers\ProgramController::class, 'allPrograms']);
     Route::get('/programs', [App\Http\Controllers\ProgramController::class, 'index']);
    Route::get('/admin/program/{id}', [App\Http\Controllers\ProgramController::class, 'show']);
    Route::post('/admin/program', [App\Http\Controllers\ProgramController::class, 'store']);
    Route::put('/admin/program/{id}', [App\Http\Controllers\ProgramController::class, 'update']);
    Route::delete('/admin/program/{id}', [App\Http\Controllers\ProgramController::class, 'destroy']);

