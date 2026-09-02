<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = User::find($this->record->id);
        $user->referralData()->create([
            'referral_code' => AuthController::generateReferralCode(),
        ]);

        $user->geo()->create([
            'ip' => ip(),
            'country_code' => country_code(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
