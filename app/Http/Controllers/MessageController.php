<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Escalation;
use \App\Service\AIChatService;
use App\Models\Message;
use App\Service\AgentQueueService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request, Conversation $conversation) {

        $request->validate([
            'message' => 'required'
        ]);

        if($conversation->agent) {
            return "Chatting with AGent now!";
        }
        
        Message::create([
            'conversation_id' => $conversation->id,
            'message' => $request->message,
        ]);
        
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
            'data' => $response['choices'][0]['message']['content'],
        ]);
        
        
        
    }
    
    public function shouldTransferToAgent($conversation_id) {
        $ai_message_count = Message::where(['conversation_id' => $conversation_id, 'direction' => 'outbound'])->count();
        return $ai_message_count >= env('AI_RESPONSE_LIMIT', 5);
    }
    
    public function chatWithAI($conversation_id, $message) {
        $response = AIChatService::sendMessageToAI($message);
        
        Message::create([
            'conversation_id' => $conversation_id,
            'message' => $response['choices'][0]['message']['content'],
            'direction' => 'outbound', // Assuming this is an outbound message
        ]);
        
        return $response;
    }
    
    public function escalateConversation(Conversation $conversation) {
        
        $agent = app(AgentQueueService::class)->assignAgent($conversation);

        if(!$agent) {
            return response()->json(['status' => false, 'message' => 'No available agents at the moment.'], 503);
        }
        
        Escalation::create([
            'conversation_id' => $conversation->id,
            'escalated_to' => $agent->id,
            'escalated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Please what while I connect you to an agent.',
            'agent' => $agent,
        ]);
        
    }
}
