<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function messages(): HasMany {
        return $this->hasMany( Message::class);
    }

    public function escalation(): HasOne {
        return $this->hasOne(Escalation::class);
    }

    public function pendingChatPool(): HasOne {
        return $this->hasOne(PendingChatPool::class);
    }
}
