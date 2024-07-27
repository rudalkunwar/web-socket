<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');

        event(new MessageSent($message));

        return response()->json(['message' => 'Message sent successfully!']);
    }
    public function test(Request $request)
    {
        return response()->json($request->all());
    }
}
