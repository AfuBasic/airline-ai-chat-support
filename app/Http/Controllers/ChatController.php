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

    public function joinChat(Request $request, Conversation $conversation) {
        if (!$conversation) {
            return response()->json(['status' => false, 'message' => 'Conversation not found'], 404);
        }

        // Logic to join the chat can be added here
        $conversation->agent_id = $request->user()->id; 
        $conversation->save();

        return response()->json(['status'=> true,'message'=> '']);
}
