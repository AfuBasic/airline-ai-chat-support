<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Events\NewMessgeSentEvent;

class ChatController extends Controller
{
    public function startChat() {
        $conversation = Conversation::create([
            "started_at" => now(),
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => 'Chat Started',
            'direction' => 'inbound', 
            'message_type' => 'event',
        ]);
        
        event(new NewMessgeSentEvent($message));
        
        return $conversation;
    }
    
    public function joinChat(Request $request, Conversation $conversation) {
        if (!$conversation) {
            return response()->json(['status' => false, 'message' => 'Conversation not found'], 404);
        }

        /* 
            Add an agent to an ongoing/escalated chat
        */

        $conversation->agent_id = $request->user()->id; 
        $conversation->save();
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->user()->name . ' has joined the conversation',
            'direction' => 'outbound', 
            'message_type' => 'event',
        ]);
        
        event(new NewMessgeSentEvent($message));

        return response()->json(['status'=> true,'data'=> '']);
    }

    /**
     * Get messages for a conversation
     * 
     * @return JsonResponse
     */
    public function getMessages(Conversation $conversation): JsonResponse {
        $messages = $conversation->messages()->latest()->paginate();
        
        return response()->json([
            'status' => true,
            'data' => $messages
        ]);
    }
}