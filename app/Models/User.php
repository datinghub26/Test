<?php

namespace App\Models;

use App\Events\NotificationEvent;
use Coderflex\LaravelTicket\Models\Message as TicketMessage;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName, CanResetPasswordContract, MustVerifyEmail
{
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'points',
        'privacy',
        'remember_token',
        'exp',
        'level',
        'role',
        'is_banned',
        'last_activity_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'password' => 'hashed',
            'privacy' => 'boolean',
        ];
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function getFilamentName(): string
    {
        return $this->email;
    }

    public function avatar(): string
    {
        $avatar = !empty($this->avatar) ? $this->avatar : 1;
        return asset("assets/avatars/memoji_{$avatar}.png");
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $avatar = !empty($attributes['avatar'] ?? null) ? $attributes['avatar'] : 1;
                return asset("assets/avatars/memoji_{$avatar}.png");
            }
        );
    }

    public function isAdmin(): bool
    {
        return $this->role == 'admin';
    }

    public function isCompletedProfile(): bool
    {
        return $this->username && $this->avatar;
    }

    public function referralData(): HasOne
    {
        return $this->hasOne(UsersReferralData::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(UsersReferral::class, 'referred_by_id');
    }

    public function geo(): HasOne
    {
        return $this->hasOne(UsersGeoData::class);
    }

    public function levelRank(): HasOne
    {
        return $this->hasOne(Level::class, 'level', 'level');
    }

    public function cashouts(): HasMany
    {
        return $this->hasMany(CashoutRequest::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function ticket_messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function streaksClaims(): HasMany
    {
        return $this->hasMany(StreaksClaim::class);
    }

    public function todayPoints(): int
    {
        return $this->leads()
            ->whereDate('created_at', today())
            ->sum('points');
    }

    public function levelProgress(): float
    {
        if (!$this->levelRank)
            return 0;

        $currentExp = $this->exp;
        $levelExp = $this->levelRank->exp;
        $nextLevelExp = $this->levelRank->exp_to_next_level;
        return round(($currentExp - $levelExp) / ($nextLevelExp - $levelExp) * 100, 2);
    }

    public function addNotification(string $title, string $message, string $type = 'success', string $href = null): void
    {
        $this->alerts()->create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'href' => $href,
        ]);
    }

    /**
     * Updates the user's points and level based on the provided points.
     *
     * @param int $points
     * @return void
     */
    public function updateUserPointsAndLevel(int $points): void
    {
        $currentPoints = $this->points;
        $newPoints = $currentPoints + $points;

        $experiencePerThousand = setting('level.exp_per_dollar', 300);
        $experiencePoints = ($points / 1000) * $experiencePerThousand;
        $newExperiencePoints = $this->exp + $experiencePoints;


        $this->updateUserLevelBasedOnExperience($newExperiencePoints);
        $this->update([
            'points' => $newPoints,
            'exp' => $newExperiencePoints,
        ]);

        if ($this->referred_by && setting('referral.enable_rewards', true)) {
            $referredUser = $this->referred_by->referredUser;
            $commission = sub_percentage($points, 95);
            $referredUser->referralData->increment('referral_points', $commission);
            $referredUser->updateUserPointsAndLevel($commission);
            $referredUser->leads()->create([
                'name' => 'Referral Commission',
                'points' => $commission,
                'payout' => 0,
                'ip' => ip(),
                'country_code' => country_code(),
            ]);
            $referredUser->addNotification('Referral Commission', "You have received $commission ERC commission from a referred user.");
        }
    }

    /**
     * Updates the user's level based on their experience points.
     *
     * @param float $experiencePoints
     * @return void
     */
    private function updateUserLevelBasedOnExperience(float $experiencePoints): void
    {
        $newLevel = Level::where('exp', '<=', $experiencePoints)
            ->orderByDesc('exp')
            ->first();

        if (!$newLevel)
            return;

        if ($newLevel->level <= $this->level)
            return;

        if (setting('level.enable_rewards', true)) {
            $rewardPoints = $newLevel->reward_points;
            $this->increment('points', $rewardPoints);
            $this->leads()->create([
                'name' => 'Level Up',
                'points' => $rewardPoints,
                'payout' => 0,
                'ip' => ip(),
                'country_code' => country_code(),
            ]);
            $this->addNotification('Level Up', "You have been rewarded {$rewardPoints} ERC for reaching level {$newLevel->level}.");
        } else {
            $this->addNotification('Level Up', "You have reached level {$newLevel->level}.", 'info');
        }

        $this->update([
            'level' => $newLevel->level,
        ]);
    }

}
