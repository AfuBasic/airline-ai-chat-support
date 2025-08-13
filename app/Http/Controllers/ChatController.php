<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
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
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->user()->name . ' has joined the conversation',
            'direction' => 'outbound', // Assuming this is an outbound message
            'message_type' => 'event',
        ]);
        
        return response()->json(['status'=> true,'data'=> '']);
    }
}