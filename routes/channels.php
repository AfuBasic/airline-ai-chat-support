<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('agent-conversation.{id}', function ($conversation, $id) {
    return (int) $conversation->id === (int) $id;
});

Broadcast::channel('public-conversation.{id}', function () {
    return true;
});
