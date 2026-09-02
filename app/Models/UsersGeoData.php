<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersGeoData extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'country_code',
        'user_agent',
        'latest_user_agent',
        'latest_login_ip',
        'latest_login_at',
    ];
}
