<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('get-posts', [PostController::class, 'index']);
    Route::get('get-one-post/{id}', [PostController::class, 'getOnePost']);
    Route::post('add-post', [PostController::class, 'store']);
    Route::get('edit-post/{id}', [PostController::class, 'edit']);
    Route::post('update-post/{id}', [PostController::class, 'update']);
    Route::post('delete-post/{id}', [PostController::class, 'delete']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
