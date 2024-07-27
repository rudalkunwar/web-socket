<?php

use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('/broadcast', function () {
    $pusher = new Pusher(
        env('PUSHER_APP_ID'),
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        array('cluster' => env('PUSHER_APP_CLUSTER'))
    );
    $pusher->trigger('my-channel', 'hello-event', array('message' => 'Hello from Laravel!'));
    return 'Message broadcasted!';
});

require __DIR__ . '/auth.php';
