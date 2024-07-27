<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('my-channel', function ($user) {
    // You can add your authorization logic here.
    // Return true if the user is authorized to listen to this channel.
    return true;
});
