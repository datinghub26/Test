<?php

namespace App\Jobs;

use App\Helpers\OffersHelper;
use App\Models\Offer;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchOffersJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $provider)
    {
    }

    public function handle(): void
    {
        $offers = OffersHelper::getProviderOffers($this->provider);
        if ($offers->isEmpty()) {
            $this->deleteProviderOffers($this->provider);
            return;
        }

        DB::transaction(function () use ($offers) {
            $offers->chunk(1000)->each(fn($chunk) => $this->upsertOffers($chunk));
        });
    }

    private function deleteProviderOffers($provider): void
    {
        Offer::where('provider', $provider)->delete();
    }

    private function upsertOffers($offers): void
    {
        Offer::upsert(
            values: $offers->toArray(),
            uniqueBy: ['offer_id', 'provider'],
            update: [
                'title',
                'description',
                'instructions',
                'requirements',
                'image',
                'link',
                'points',
                'payout',
                'categories',
                'countries',
                'devices',
                'events'
            ]
        );
    }
}
