<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$offer = App\Models\Offer::where('offer_id', '92536')->first();
if ($offer) {
    $offer->points = 423766;
    $offer->payout = 847.53;
    $offer->instructions = [
        'Click "Start Offer" and register a new account on Cases.gg.',
        'Make a minimum deposit of $5+ to unlock higher rewards.',
        'Play games and level up (Reach Level 5, 10, 20, 30, 40, 50, and 60).',
        'Rewards are credited automatically at each completed stage!'
    ];
    $offer->events = [
        ['name' => 'Register for Cases.gg', 'payout' => 0.02, 'points' => 10],
        ['name' => 'Make a Deposit of $5+', 'payout' => 2.65, 'points' => 1326],
        ['name' => 'Complete Level 5', 'payout' => 0.70, 'points' => 348],
        ['name' => 'Complete Level 10', 'payout' => 3.48, 'points' => 1740],
        ['name' => 'Complete Level 20', 'payout' => 25.19, 'points' => 12597],
        ['name' => 'Complete Level 30', 'payout' => 53.04, 'points' => 26520],
        ['name' => 'Complete Level 40', 'payout' => 99.45, 'points' => 49725],
        ['name' => 'Complete Level 50', 'payout' => 232.05, 'points' => 116025],
        ['name' => 'Complete Level 60', 'payout' => 430.95, 'points' => 215475],
    ];
    $offer->save();
    echo "OFFER_FIXED_SUCCESSFULLY\n";
} else {
    echo "OFFER_NOT_FOUND\n";
}
unlink(__FILE__);
