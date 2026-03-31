<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    if (\App\Models\Conversation::where('id', $conversationId)
        ->where(function($query) use ($user) {
            $query->where('user_one', $user->id)
                ->orWhere('user_two', $user->id);
        })->exists()) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});
