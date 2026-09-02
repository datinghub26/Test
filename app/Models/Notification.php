<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'href',
        'is_read',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(fn($notification) => event(new \App\Events\NotificationCreated($notification)));
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
