<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OfferwallController extends Controller
{
    public function ogads(Request $request)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $offers = self::getOgAdsApiOffers();
        $categories = $offers->pluck('categories')->flatten()->unique();
        $devices = $offers->pluck('devices')->flatten()->unique();

        if ($request->input('category')) {
            $offers = $offers->filter(function ($offer) use ($request) {
                return in_array($request->input('category'), $offer['categories']);
            });
        }

        if ($request->input('device')) {
            $offers = $offers->filter(function ($offer) use ($request) {
                return in_array($request->input('device'), $offer['devices']);
            });
        }

        if ($request->input('sort')) {
            if ($request->input('sort') == 'points_high') {
                $offers = $offers->sortByDesc('total_points');
            } else {
                $offers = $offers->sortBy('total_points');
            }
        }

        return view('user.offerwall.ogads', compact('offers', 'categories', 'devices'));
    }

    public static function getOgAdsApiOffers(): Collection
    {
        $ip = ip();
        $userAgent = request()->userAgent();
        $cacheKey = "ogads:offers:$ip:$userAgent";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::withToken('31144|2du6umKryes8FmVsBCYVxwuJPVJeEqurELa3LDZVf719c7d4')
            ->get("https://unlockcontent.net/api/v2", [
                'ip' => $ip,
                'user_agent' => $userAgent,
            ]);

        if ($response->failed()) {
            return collect();
        }

        $data = $response->json();
        if (!isset($data['offers']) || count($data['offers']) <= 0) {
            return collect();
        }

        $userId = auth()->id() ?? '{user_id}';
        $offers = collect($data['offers'])->map(function ($data) use ($userId) {
            $url = str_replace(
                ['aff_sub4=', '&aff_sub5='],
                ["aff_sub4=$userId", ''],
                $data['link']
            );

            return [
                'provider' => 'OgAds',
                'id' => $data['offerid'],
                'title' => $data['name_short'],
                'name' => $data['name_short'],
                'description' => $data['description'],
                'image' => $data['picture'],
                'total_points' => $data['payout'] * setting('offers.ogads.rate', 500),
                'categories' => [$data['ctype']],
                'events' => [
                    [
                        'name' => 'Base',
                        'points' => $data['payout'] * setting('offers.ogads.rate', 500),
                    ]
                ],
                'things_to_know' => [],
                'url' => $url,
                'devices' => collect(explode(',', $data['device']))->map(function ($device) {
                    $device = trim($device);
                    return match (strtolower($device)) {
                        'ios', 'ipad', 'iphone' => 'iOS',
                        'android' => 'Android',
                        'pc', 'mac', 'desktop' => 'Desktop',
                        default => $device,
                    };
                })->unique()->values()->toArray(),
            ];
        });

        Cache::put($cacheKey, $offers, now()->addHour());
        return $offers;
    }

}
