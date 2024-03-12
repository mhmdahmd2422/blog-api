<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{room}', function ($user, ChatRoom $room) {
    if($user->rooms->contains($room)) {
        return [
          'id' => $user->id,
          'name' => $user->name,
        ];
    }
});
