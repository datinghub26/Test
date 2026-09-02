<?php

namespace App\Models;

use App\Events\MessageCreated;
use App\Events\MessageDeleted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['user_id', 'message'];

    protected static function boot()
    {
        parent::boot();
        static::created(fn() => MessageCreated::dispatch());
        static::deleted(fn() => MessageDeleted::dispatch());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function extractUsernameFromMessage(): ?string
    {
        if (preg_match('/@(\w+)/', $this->message, $matches)) {
            return $matches[1]; // Return the captured username
        }
        return null;
    }

    public function containsUsernameMention(): bool
    {
        $username = $this->extractUsernameFromMessage();
        return $username && User::where('username', $username)->exists();
    }

    public function getMentionedUser(): ?User
    {
        $username = $this->extractUsernameFromMessage();
        return $username ? User::where('username', $username)->first() : null;
    }
}
