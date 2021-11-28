<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatRoomController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $chat = ChatRoom::create([
            'user_id' => auth()->user()->id,
            'username' => $request['username'],
            'secret' => Str::random(10)
        ]);

        return response($chat, 200);
    }
}
