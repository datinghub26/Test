<?php

use App\Console\Commands\ArchiveLogs;
use App\Console\Commands\AwardLeaderboard;
use App\Console\Commands\ReleasePendingOffers;
use App\Console\Commands\UpdateOffers;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command(AwardLeaderboard::class)->weeklyOn(7, '23:55');
Schedule::command(UpdateOffers::class)->everySixHours();
Schedule::command(ArchiveLogs::class)->weekly()->at('00:00');
Schedule::command(ReleasePendingOffers::class)->hourly();

Schedule::command('queue:work --stop-when-empty')->everyMinute();
