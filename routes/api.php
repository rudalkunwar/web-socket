<?php

use App\Http\Controllers\MovieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\ExampleEvent;
use App\Http\Controllers\MessageController;
use App\Events\MyEvent;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('movie/add', [MovieController::class, 'store']);