<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasterController;
use App\Http\Controllers\testApi;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;

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
    Route::post('/login', [TasterController::class, 'login'])->middleware('api');
    Route::put('/update/{user_id}', [TasterController::class, 'update'])->middleware('api');
    Route::delete('/delete/{user_id}', [TasterController::class, 'destroy'])->middleware('api');
    Route::get('/getUserName/{user_id}', [TasterController::class, 'getUserName'])->middleware('api');
});

// Shop Controller
Route::prefix('shops')->group(function () {
    Route::get('/indexAllShops', [ShopController::class, 'index'])->middleware('api');
    Route::post('/create', [ShopController::class, 'store'])->middleware('api');
});

// Item Controller
Route::prefix('items')->group(function () {
    Route::get('/indexShopItems/{item_id}', [ItemController::class, 'indexShopItems'])->middleware('api');
    Route::post('/create', [ItemController::class, 'createItem'])->middleware('api');
    Route::get('/show/{item_id}', [ItemController::class, 'showItem'])->middleware('api');
    Route::get('/show/TicketData/{item_id}', [ItemController::class, 'showItemTicket'])->middleware('api');
    Route::put('/update/{item_id}', [ItemController::class, 'updateItem'])->middleware('api');
    Route::delete('/delete/{item_id}', [ItemController::class, 'deleteItem'])->middleware('api');
});

// Ticket Controller
Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'indexAll'])->middleware('api');
    Route::get('/userTickets/{buyer_id}', [TicketController::class, 'indexUserTickets'])->middleware('api');
    Route::get('/ShopTickets/{shop_id}', [TicketController::class, 'indexShopTickets'])->middleware('api');
    Route::post('/create', [TicketController::class, 'store'])->middleware('api');
    Route::put('/{ticket_id}/{status}', [TicketController::class, 'updateStatus'])->middleware('api');
    Route::delete('/delete/{ticket_id}', [TicketController::class, 'deleteTicket'])->middleware('api');
});

// Route for uploading images
Route::post('/upload-image', [ImageController::class, 'uploadToImgur']);
// Route for sending password reset email
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('api');
// Route for resetting the password
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('api');
