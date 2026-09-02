<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use App\Events\OnlineUsersCountUpdated;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('online-users', function ($user) {
    if ($user) {
        $user->last_activity_at = now();
        $user->save();
        return ['id' => $user->id];
    }
    return false;
});
