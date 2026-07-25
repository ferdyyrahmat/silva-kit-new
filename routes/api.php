<?php

use App\Http\Controllers\Api\SwaggerDocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [SwaggerDocsController::class, 'userProfile'])->name('api.user');
});
