<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasterController;
use App\Http\Controllers\testApi;
use App\Http\Controllers\UserController;

# Route::get('/tasters', [TasterController::class, 'index']);
# Route::post('/tasters', [TasterController::class, 'store']);
Route::get('/tests', [testApi::class, 'index']);
Route::post('/tests/register', [testApi::class, 'register']);
Route::put('/tests/update/{id}', [testApi::class, 'update']);
Route::delete('/tests/delete/{id}', [testApi::class, 'delete']);
# Route::post('login', 'Api/AuthController@login');
# Route::post('register', 'Api/AuthController@register');

Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
