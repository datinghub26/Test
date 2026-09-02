<?php

namespace App\Console\Commands;

use App\Helpers\CampaignHelper;
use Illuminate\Console\Command;

class UpdateCampaignProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-campaign-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CampaignHelper::handleCampaignEvents();
    }
}
