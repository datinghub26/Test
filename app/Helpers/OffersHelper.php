<?php

namespace App\Helpers;

use App\Models\Offer;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class OffersHelper
{

    public static function getAllProviders(): array
    {
        return [
            'Admantum',
            'Torox',
            'Monlix',
            'Adscendmedia',
            'Notik',
        ];
    }

    public static function getProviderOffers(string $provider): LazyCollection
    {
        return match ($provider) {
            'Admantum' => self::getAdmantumApiOffers(),
            'Torox' => self::getToroxApiOffers(),
            'Monlix' => self::getMonlixApiOffers(),
            'Adscendmedia' => self::getAdscendmediaApiOffers(),
            'Notik' => self::getNotikApiOffers(),
            default => LazyCollection::make(),
        };
    }

    public static function getAdmantumApiOffers(): LazyCollection
    {
        if (!setting('offers.admantum.enabled'))
            return LazyCollection::make();

        $response = Http::get("https://admantum.com/api/v3/offers", [
            'appid' => setting('offers.admantum.appid'),
            "uid" => 1,
        ]);

        if (!$response->successful())
            return LazyCollection::make();

        $data = $response->json();
        if (!isset($data['offers']) || count($data['offers']) === 0)
            return LazyCollection::make();


        $offers = [];
        foreach ($data['offers'] as $offer) {
            $offers[] = [
                'provider' => 'Admantum',
                'offer_id' => $offer['offer_id'],
                'title' => $offer['offer_title'],
                'description' => $offer['offer_description'],
                'instructions' => json_encode($offer['offer_instructions']),
                'requirements' => $offer['offer_requirements'],
                'image' => $offer['offer_image'],
                'payout' => $offer['offer_payout'],
                'points' => $offer['offer_virtual_currency'],
                'link' => Str::replace('user=1', 'user={user_id}', $offer['offer_link']),
                'countries' => json_encode($offer['offer_countries']),
                'categories' => self::reformatCategories($offer['offer_categories']),
                'devices' => self::reformatDevices($offer['offer_devices']),
                'events' => collect($offer['offer_events'])->map(function ($event) {
                    return [
                        'name' => $event['event_name'],
                        'points' => $event['event_virtual_currency'],
                        'payout' => $event['event_payout'],
                    ];
                })->toJson(),
            ];
        }

        return LazyCollection::make($offers);
    }

    public static function getToroxApiOffers(): LazyCollection
    {
        if (!setting('offers.torox.enabled'))
            return LazyCollection::make();

        $response = Http::get("https://torox.io/api", [
            'pubid' => setting('offers.torox.pubid'),
            'appid' => setting('offers.torox.appid'),
            'secretkey' => setting('offers.torox.secretkey'),
            'format' => 'json',
        ]);

        if (!$response->successful())
            return LazyCollection::make();

        $data = $response->json();
        if (!isset($data['response']['offers']) || count($data['response']['offers']) === 0)
            return LazyCollection::make();


        $offers = [];
        foreach ($data['response']['offers'] as $offer) {
            $offers[] = [
                'provider' => 'Torox',
                'offer_id' => $offer['offer_id'],
                'title' => $offer['offer_name'],
                'description' => $offer['offer_desc'],
                'instructions' => json_encode([]),
                'requirements' => null,
                'image' => $offer['image_url'],
                'payout' => $offer['payout'],
                'points' => $offer['amount'],
                'link' => Str::replace('[USER_ID]', '{user_id}', $offer['offer_url']),
                'countries' => json_encode($offer['countries']),
                'categories' => self::reformatCategories([$offer['payout_type']]),
                'devices' => self::reformatDevices([$offer['device']]),
                'events' => json_encode([]),
            ];
        }

        return LazyCollection::make($offers);
    }

    public static function getMonlixApiOffers(): LazyCollection
    {
        if (!setting('offers.monlix.enabled'))
            return LazyCollection::make();

        $response = Http::get("https://api.monlix.com/api/static", [
            'appid' => setting('offers.monlix.api_key'),
            'token' => md5(setting('offers.monlix.api_key') . setting('offers.monlix.secret_key')),
        ]);

        if (!$response->successful())
            return LazyCollection::make();

        $data = $response->json();
        if (!isset($data['campaigns']) || count($data['campaigns']) === 0)
            return LazyCollection::make();


        $offers = [];
        foreach ($data['campaigns'] as $offer) {

            if (str_contains($offer['os'], ',')) {
                $offer['os'] = explode(',', $offer['os']);
            } else {
                $offer['os'] = [$offer['os']];
            }

            $offers[] = [
                'provider' => 'Monlix',
                'offer_id' => $offer['id'],
                'title' => $offer['name'],
                'description' => $offer['description'],
                'instructions' => json_encode([]),
                'requirements' => null,
                'image' => $offer['image'],
                'payout' => $offer['payout'],
                'points' => floatval($offer['payout']) * setting('offers.monlix.payout_rate', 400) ?? 400,
                'link' => Str::replace('{{userid}}', '{user_id}', $offer['url']),
                'countries' => json_encode($offer['countries']),
                'categories' => self::reformatCategories($offer['categories']),
                'devices' => self::reformatDevices($offer['os']),
                'events' => collect($offer['goals'])->map(function ($event) {
                    return [
                        'name' => $event['name'],
                        'points' => floatval($event['payouts'][0]['payout']) * setting('offers.monlix.payout_rate', 400) ?? 400,
                        'payout' => floatval($event['payouts'][0]['payout']),
                    ];
                })->toJson(),
            ];
        }

        return LazyCollection::make($offers);
    }

    public static function getAdscendmediaApiOffers(): LazyCollection
    {
        if (!setting('offers.adscendmedia.enabled'))
            return LazyCollection::make();

        $pub_id = setting('offers.adscendmedia.pub_id');
        $response = Http::withBasicAuth($pub_id, setting('offers.adscendmedia.api_key'))
            ->get("https://api.adscendmedia.com/v1/publisher/$pub_id/offers.json");

        if (!$response->successful())
            return LazyCollection::make();

        $data = $response->json();
        if (!isset($data['offers']) || count($data['offers']) === 0)
            return LazyCollection::make();


        $offers = [];
        foreach ($data['offers'] as $offer) {
            $offer['os'] = match ((int)$offer['target_system']) {
                31 => ['desktop'],
                30 => ['android', 'ios'],
                40, 56 => ['android'],
                50, 51, 52 => ['ios'],
                default => ['desktop', 'ios', 'android'],
            };

            $offers[] = [
                'provider' => 'Adscendmedia',
                'offer_id' => $offer['offer_id'],
                'title' => $offer['name'],
                'description' => $offer['description'],
                'instructions' => json_encode([]),
                'requirements' => null,
                'image' => $offer['creatives'][0]['url'] ?? null,
                'payout' => floatval($offer['payout']),
                'points' => floatval($offer['payout']) * setting('offers.adscendmedia.payout_rate', 400) ?? 400,
                'link' => $offer['click_url'] . '&sub1={user_id}',
                'countries' => json_encode($offer['countries']),
                'categories' => self::reformatCategories($offer['category_id']),
                'devices' => self::reformatDevices($offer['os']),
                'events' => collect($offer['events'])->map(function ($event) {
                    return [
                        'name' => $event['event_name'],
                        'points' => floatval($event['event_revenue']) * setting('offers.monlix.payout_rate', 400) ?? 400,
                        'payout' => floatval($event['event_revenue']),
                    ];
                })->toJson(),
            ];
        }

        return LazyCollection::make($offers);
    }

    public static function getNotikApiOffers(): LazyCollection
    {
        if (!setting('offers.notik.enabled'))
            return LazyCollection::make();

        $response = Http::get("https://notik.me/api/v2/get-offers/all", [
            'api_key' => setting('offers.notik.api_key'),
            'pub_id' => setting('offers.notik.pub_id'),
            'app_id' => setting('offers.notik.app_id'),
        ]);

        $data = $response->json();
        if (isset($data['offers']['has_more_pages']) && isset($data['offers']['next_page_url'])) {
            $nextPage = $data['offers']['next_page_url'];
            $offers = self::getNotikApiOfferPerPage($response);
            while ($nextPage !== null) {
                $response = Http::get($nextPage);
                $offers = $offers->merge(self::getNotikApiOfferPerPage($response));
                $data = $response->json();
                $nextPage = $data['offers']['next_page_url'] ?? null;
            }
            return $offers;
        }

        return self::getNotikApiOfferPerPage($response);
    }

    private static function getNotikApiOfferPerPage(Response $response): LazyCollection
    {
        if (!$response->successful())
            return LazyCollection::make();

        $data = $response->json();
        if (!isset($data['offers']['data']) || count($data['offers']['data']) === 0)
            return LazyCollection::make();

        $offers = [];
        foreach ($data['offers']['data'] as $offer) {
            if (isset($offer['events'])) {
                $events = collect($offer['events'])->map(function ($event) {
                    return [
                        'name' => $event['name'],
                        'points' => floatval($event['payout']) * setting('offers.notik.payout_rate', 400) ?? 400,
                        'payout' => floatval($event['payout']),
                    ];
                })->toJson();
            } else {
                $events = json_encode([
                    [
                        'name' => $offer['description1'],
                        'points' => floatval($offer['payout']) * setting('offers.notik.payout_rate', 400) ?? 400,
                        'payout' => floatval($offer['payout']),
                    ],
                ]);
            }

            $instructions = [
                $offer['description1'],
                $offer['description2'],
                $offer['description3'],
            ];

            $instructions = array_filter($instructions, function ($instruction) {
                return !empty($instruction);
            });

            $offers[] = [
                'provider' => 'Notik',
                'offer_id' => $offer['offer_id'],
                'title' => $offer['name'],
                'description' => $offer['description1'],
                'instructions' => json_encode($instructions),
                'requirements' => null,
                'image' => $offer['image_url'],
                'payout' => floatval($offer['payout']),
                'points' => floatval($offer['payout']) * setting('offers.notik.payout_rate', 400) ?? 400,
                'link' => Str::replace('[user_id]', '{user_id}', $offer['click_url']),
                'countries' => json_encode($offer['country_code']),
                'categories' => self::reformatCategories($offer['categories']),
                'devices' => self::reformatDevices($offer['os']),
                'events' => $events,
            ];
        }

        return LazyCollection::make($offers);
    }

    public static function getUniqueOffers(LazyCollection $offers = null): LazyCollection
    {
        if ($offers === null)
            return LazyCollection::make();

        if ($offers->isEmpty())
            return $offers;

        $existingOffers = Offer::whereIn('offer_id', $offers->pluck('offer_id'))
            ->where('provider', $offers->first()['provider'])
            ->pluck('offer_id')->toArray();

        return $offers->reject(function ($offer) use ($existingOffers) {
            return in_array($offer['offer_id'], $existingOffers);
        });
    }

    private static function reformatDevices(array $devices): false|string
    {
        $deviceMap = [
            'iphone' => 'iOS',
            'ipad' => 'iOS',
            'ios' => 'iOS',
            'iphone_ipad' => 'iOS',
            'android' => 'Android',
            'desktop' => 'Desktop',
            'windows' => 'Desktop',
            40 => 'Android',
            50 => 'iOS',
            31 => 'Desktop',
        ];

        $result = [];
        foreach ($deviceMap as $key => $value) {
            if (in_array($key, $devices) || $devices[0] === 'all' || count($devices) === 0) {
                if (!in_array($value, $result))
                    $result[] = $value;
            }
        }


        return json_encode($result);
    }

    private static function reformatCategories(array $categories): false|string
    {
        $categoryMap = [
            'Games' => 'Games',
            'Crypto' => 'Crypto',
            'Surveys' => 'Surveys',
            'survey' => 'Surveys',
            'cpe' => 'Games',
            'CPE' => 'Games',
            'cpi' => 'Apps',
            'android app' => 'Apps',
            'cpa' => 'Sign Up',
            'CPL' => 'Sign Up',
            'registration' => 'Sign Up',
            'signup' => 'Sign Up',
            'free_trial' => 'Free Trial',
            'product trial' => 'Free Trial',
            17 => 'Free Trial',
            18 => 'Apps',
            20 => 'Surveys',
            22 => 'Free Trial',
            24 => 'Sign Up',
            26 => 'Sign Up',
            29 => 'Casino',
        ];

        $result = [];
        foreach ($categoryMap as $key => $value) {
            if (in_array($key, $categories)) {
                if (!in_array($value, $result))
                    $result[] = $value;
            }
        }

        if (count($result) === 0)
            $result = ['Other'];

        return json_encode($result);
    }
}
