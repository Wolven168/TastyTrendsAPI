<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasterController;
use App\Http\Controllers\testApi;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ItemController;

// Tester Controller
Route::prefix('tests')->group(function () {
    Route::get('/', [testApi::class, 'index']);
    Route::post('/register', [testApi::class, 'register']);
    Route::post('/login', [testApi::class, 'login']);
    Route::put('/update/{id}', [testApi::class, 'update']);
    Route::delete('/delete/{id}', [testApi::class, 'delete']);
});

// Taster Controller
Route::prefix('tasters')->group(function () {
    Route::post('/register', [TasterController::class, 'register']);
    Route::post('/login', [TasterController::class, 'login']);
    Route::put('/update/{user_id}', [TasterController::class, 'update']);
    Route::delete('/delete/{user_id}', [TasterController::class, 'delete']);
    Route::get('/getUserName/{user_id}', [TasterController::class, 'getUserName']);
});

// Shop Controller
Route::prefix('shops')->group(function () {
    Route::post('/indexAllShops', [ShopController::class, 'index']);
    Route::post('/create', [ShopController::class, 'store']);
});

// Item Controller
Route::prefix('items')->group(function () {
    Route::get('/indexShopItems/{item_id}', [ItemController::class, 'indexShopItems']);
    Route::post('/create', [ItemController::class, 'createItem']);
    Route::get('/show/{item_id}', [ItemController::class, 'showItem']);
    Route::get('/show/TicketData/{item_id}', [ItemController::class, 'showItemTicket']);
    Route::put('/update/{item_id}', [ItemController::class, 'updateItem']);
    Route::delete('/delete/{item_id}', [ItemController::class, 'deleteItem']);
});

// Ticket Controller
Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'indexAll']);
    Route::get('/userTickets/{buyer_id}', [TicketController::class, 'indexUserTickets']);
    Route::get('/ShopTickets/{shop_id}', [TicketController::class, 'indexShopTickets']);
    Route::post('/create', [TicketController::class, 'store']);
    Route::put('/{ticket_id}/{status}', [TicketController::class, 'updateStatus']);
    Route::delete('/delete/{ticket_id}', [TicketController::class, 'deleteTicket']);
});

// User Route
Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

