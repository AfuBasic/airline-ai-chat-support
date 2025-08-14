<?php

namespace App\Http\Controllers;

use App\Events\NewMessgeSentEvent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class AgentMessageController extends Controller
{
    public function sendMessage(Request $request,  Conversation $conversation) {
         $request->validate([
            'message' => 'required'
        ]);


         $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->message,
            'agent_id' => $request->user()->id,
            'direction' => 'outbound',
        ]);

        event(new NewMessgeSentEvent($message));

        return $message;
    }
}
