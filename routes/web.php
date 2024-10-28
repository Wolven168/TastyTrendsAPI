<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailScent; // Adjust according to your mail class

Route::get('/test-email', function () { // Uncomment to debug
    $data = [
        'name' => 'Test User',
        'message' => 'This is a test email!',
    ];

    Mail::to('royalrex168@gmail.com')->send(new MailScent($data));

    return 'Email sent!';
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
