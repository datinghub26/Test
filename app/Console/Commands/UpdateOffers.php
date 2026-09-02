<?php

namespace App\Console\Commands;

use App\Helpers\OffersHelper;
use App\Jobs\FetchOffersJob;
use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;

class UpdateOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update offers from the providers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (OffersHelper::getAllProviders() as $provider) {
            FetchOffersJob::dispatch($provider);
        }
    }
}
