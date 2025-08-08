<?php
namespace App\Service;

use LucianoTonet\GroqLaravel\Facades\Groq;

class AIChatService
{
    public static $response;
    
    public static function sendMessageToAI($message)
    {
        self::$response = Groq::chat()->completions()->create([
            'model' => 'llama-3.1-8b-instant',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful airline assistant. Keep your responses very short and relevant to the user\'s queries.'],  
                ['role' => 'user', 'content' => $message ]
            ]
        ]);
        
        return self::$response;
    }
    
}