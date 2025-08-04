<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SnippetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes accessibles via /api/
|
*/

// Routes principales pour gérer les snippets
Route::get('/snippets', [SnippetController::class, 'index']);
Route::post('/snippets', [SnippetController::class, 'store']);
Route::get('/snippets/{id}', [SnippetController::class, 'show']);        // optionnel
Route::put('/snippets/{id}', [SnippetController::class, 'update']);      // optionnel
Route::delete('/snippets/{id}', [SnippetController::class, 'destroy']);  // optionnel
