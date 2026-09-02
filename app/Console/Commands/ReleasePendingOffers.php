<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleasePendingOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:release-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release matured pending offers whose hold duration has elapsed and credit user points';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for matured pending offers...');

        $leadsToRelease = Lead::readyToRelease()->with('user')->get();

        if ($leadsToRelease->isEmpty()) {
            $this->info('No pending offers ready for release.');
            return Command::SUCCESS;
        }

        $releasedCount = 0;
        $totalPointsReleased = 0;

        foreach ($leadsToRelease as $lead) {
            try {
                DB::transaction(function () use ($lead, &$releasedCount, &$totalPointsReleased) {
                    $lead->update([
                        'status' => 'approved',
                    ]);

                    if ($lead->user) {
                        $lead->user->updateUserPointsAndLevel(floatval($lead->points));
                    }

                    $releasedCount++;
                    $totalPointsReleased += $lead->points;

                    Log::channel('postback-hold')->info("Pending offer released automatically for Lead ID {$lead->id}", [
                        'user_id' => $lead->user_id,
                        'offer_id' => $lead->offer_id,
                        'points' => $lead->points,
                        'release_at' => $lead->release_at,
                    ]);
                });
            } catch (\Exception $e) {
                Log::channel('postback-error')->error("Failed to release pending lead {$lead->id}: " . $e->getMessage());
                $this->error("Failed to release lead #{$lead->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully released {$releasedCount} pending offers totaling {$totalPointsReleased} points.");
        return Command::SUCCESS;
    }
}
