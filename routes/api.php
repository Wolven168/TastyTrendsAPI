<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasterController;
use App\Http\Controllers\testApi;
use App\Http\Controllers\UserController;

// Tester Controller
Route::get('/tests', [testApi::class, 'index']);
Route::post('/tests/register', [testApi::class, 'register']);
Route::post('/tests/login', [testApi::class, 'login']);
Route::put('/tests/update/{id}', [testApi::class, 'update']);
Route::delete('/tests/delete/{id}', [testApi::class, 'delete']);

// Taster Controller (Copies Tester Controller)
Route::post('/tasters/register', [TasterController::class, 'register']);
Route::post('/tasters/login', [TasterController::class, 'login']);
Route::put('/tasters/update/{id}', [TasterController::class, 'update']);
Route::delete('/tasters/delete/{id}', [TasterController::class, 'delete']);

// Item Controller
Route::get('/items/indexItems', [ItemController::class, 'indexItems']);
Route::post('/items/createItem', [ItemController::class, 'createItem']);
Route::get('/items/showItem/{id}', [ItemController::class, 'showItem']);
Route::put('/items/update/{id}', [ItemController::class, 'updateItem']);
Route::delete('/items/deleteItem', [ItemController::class, 'deleteItem']);

// Shop Controller

// Ticket Controller

Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
