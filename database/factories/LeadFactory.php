<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        $offers = Offer::select([
            'id',
            'offer_id',
            'title',
            'points',
            'payout',
            'provider',
            'image',

        ])->cursor();

        $offer = $offers->random();
        return [
            'type' => 'offer',
            'name' => "$offer->provider - $offer->title ($offer->offer_id)",
            'offer_id' => $offer->offer_id,
            'offer_name' => $offer->title,
            'image' => $offer->image,
            'points' => $offer->points,
            'payout' => $offer->payout,
            'ip' => $this->faker->ipv4(),
            'country_code' => $this->faker->countryCode,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::pluck('id')->random(),
        ];
    }
}
