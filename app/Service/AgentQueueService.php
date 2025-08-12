<?php
namespace App\Service;

use App\Models\User;
use App\Models\Conversation;

class AgentQueueService
{
    public function assignAgent(Conversation $conversation)
    {
        $agent = User::where('user_type', 'agent')
            ->where('is_online', true)
            ->where('is_available', true)
            ->orderBy('active_conversations', 'asc')
            ->first();

        if (!$agent) {
            return null; // No available agent
        }

        $conversation->agent_id = $agent->id;
        $conversation->status = 'escalated';
        $conversation->save();

        // Increment workload
        $agent->increment('active_conversations');
        
        if($agent->active_conversations > 3){
            $agent->is_available = false;
            $agent->save();
        }

        return $agent;
    }
}