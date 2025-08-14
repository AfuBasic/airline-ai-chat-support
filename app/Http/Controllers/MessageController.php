<?php

namespace App\Http\Controllers;

use App\Events\NewMessgeSentEvent;
use App\Models\Conversation;
use App\Models\Escalation;
use \App\Service\AIChatService;
use App\Models\Message;
use App\Models\PendingChatPool;
use App\Service\AgentQueueService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request, Conversation $conversation) {
        
        $request->validate([
            'message' => 'required'
        ]);
        
        //    return $conversation->PendingChatPool;
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->message,
        ]);
        
        if($conversation->agent || $conversation->PendingChatPool) {
            
            /* 
                Broadcast the message
            */
            event(new NewMessgeSentEvent($message));
            return response()->json([
                'status' => true,
                'data' => $message,
            ]);
        }
        
        $message = "Please transfer me to an agent";
        
        if($conversation->agent == null && !$this->shouldTransferToAgent($conversation->id)) {
            $message = $request->message;
        }
        
        if($this->shouldTransferToAgent($conversation->id)) {
            return $this->escalateConversation($conversation);
        }
        
        $response = $this->chatWithAI($conversation->id, $message);
        return response()->json([
            'status' => true,
            'data' => $response,
        ]);
        
        
        
    }
    
    public function shouldTransferToAgent($conversation_id) {
        $ai_message_count = Message::where(['conversation_id' => $conversation_id, 'direction' => 'outbound'])->count();
        return $ai_message_count >= env('AI_RESPONSE_LIMIT', 5);
    }
    
    public function chatWithAI($conversation_id, $message) {
        $response = AIChatService::sendMessageToAI($message);
        
        $message = Message::create([
            'conversation_id' => $conversation_id,
            'message' => $response['choices'][0]['message']['content'],
            'direction' => 'outbound', // Assuming this is an outbound message
        ]);
        
        return $message;
    }
    
    public function escalateConversation(Conversation $conversation) {
        
        $agent = app(AgentQueueService::class)->assignAgent($conversation);
        
        if(!$agent) {
            PendingChatPool::create([
                'conversation_id'=> $conversation->id,
            ]);
            return response()->json(['status' => false, 'message' => 'Connecting you to a human agent, please wait for a few minutes']);
        }
        
        Escalation::create([
            'conversation_id' => $conversation->id,
            'escalated_to' => $agent->id,
            'escalated_at' => now(),
        ]);
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $agent->name . ' has joined the conversation',
            'direction' => 'outbound', // Assuming this is an outbound message
            'message_type' => 'event',
        ]);
        
         event(new NewMessgeSentEvent($message));

        return response()->json([
            'status' => true,
            'data' => $message,
            'agent' => $agent,
        ]);
        
    }
}
