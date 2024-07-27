<?php

use App\Http\Controllers\MovieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ExampleEvent;
use App\Http\Controllers\MessageController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('movie/add', [MovieController::class, 'store']);


Route::get('/test-broadcast', function () {
    event(new ExampleEvent('Hello World'));
    return 'Broadcast event sent!';
});


Route::middleware(['cors'])->post('/test', [MessageController::class, 'test']);

Route::post('/send-message', [MessageController::class, 'sendMessage']);
