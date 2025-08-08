<?php

namespace App\Http\Controllers;

use \App\Service\AIChatService;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request, $conversation_id) {
        $request->validate([
            'message'=> 'required'
        ]);


        Message::create([
            'conversation_id' => $conversation_id,
            'message' => $request->message,
        ]);

        $response = AIChatService::sendMessageToAI($request->message);

        Message::create([
            'conversation_id' => $conversation_id,
            'message' => $response['choices'][0]['message']['content'],
            'direction' => 'outbound', // Assuming this is an outbound message
        ]);

        return response()->json([
            'status' => true,
            'data' => $response['choices'][0]['message']['content'],
        ]);
    }
}
