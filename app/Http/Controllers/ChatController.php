<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function startChat() {
        $chat = Conversation::create([
            "started_at" => now(),
        ]);

        return $chat;
    }
}
