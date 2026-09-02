<?php

namespace App\Providers;

use App\Models\Offer;
use App\Models\Provider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('sidebar', $this->userSidebar());
        });
    }


    protected function userSidebar(): array
    {
        try {
            $countryCode = strtoupper(country_code());
            $categoriesMenu = Cache::remember("sidebar_data_$countryCode", now()->addMinutes(15), function () use ($countryCode) {
                $categories = Offer::query()
                    ->whereJsonContains('countries', $countryCode)
                    ->pluck('categories')
                    ->flatten();

                $categoryCounts = $categories->countBy()->toArray();
                uksort($categoryCounts, function ($a, $b) {
                    return strnatcasecmp($a, $b);
                });

                $categoriesMenu = [];
                foreach ($categoryCounts as $category => $count) {
                    $categoriesMenu[] = [
                        'name' => $category,
                        'route' => "offers",
                        'params' => "category=$category",
                        'icon' => 'heroicon-s-tag',
                        'badge' => [
                            'text' => $count,
                            'color' => 'primary',
                        ]
                    ];
                }

                return $categoriesMenu;
            });


            return [
                [
                    'name' => 'Home',
                    'route' => 'home',
                    'icon' => 'heroicon-s-home',
                    'hide' => auth()->check(),
                ],
                [
                    'name' => 'Earn',
                    'route' => 'earn',
                    'icon' => 'heroicon-s-currency-pound',
                ],
                [
                    'name' => 'Offers',
                    'icon' => 'heroicon-s-fire',
//                    'header' => 'Offers',
                    'submenu' => array_merge(
                        [
                            [
                                'name' => 'All',
                                'route' => 'offers',
                                'icon' => 'heroicon-s-rocket-launch',
                            ],
                        ],
                        $categoriesMenu,
                        
                    )
                ],
                
                [
                    'name' => 'Offerwall',
                    'route' => 'partners',
                    'icon' => 'heroicon-s-sparkles',
                    'badge' => [
                        'text' => Provider::where('type', 'offer')->where('is_active', true)->count(),
                        'color' => 'primary',
                    ]
                ],
                
                [
                    'name' => 'Surveywall',
                    'route' => 'surveys',
                    'icon' => 'heroicon-s-pencil-square',
                    'badge' => [
                        'text' => Provider::where('type', 'survey')->where('is_active', true)->count(),
                        'color' => 'primary',
                    ]
                ],
                [
                    'name' => 'Withdraw',
                    'route' => 'shop',
                    'icon' => 'heroicon-s-shopping-cart', // Heroicons solid
//                    'header' => 'Shop',
                ],
                [
                    'name' => 'Leaderboard',
                    'route' => 'leaderboard',
                    'icon' => 'heroicon-s-chart-bar', // Alternative for trophy
//                    'header' => 'Others',
                ],
                [
                    'name' => 'Referrals',
                    'route' => 'referrals',
                    'icon' => 'heroicon-s-users', // Heroicons solid
                ],
                [
                    'name' => 'Rewards',
                    'route' => 'rewards',
                    'icon' => 'heroicon-s-gift', // Heroicons solid
                ],
                [
                    'name' => 'Profile',
                    'route' => 'profile',
                    'icon' => 'heroicon-s-user', // Heroicons solid
//                    'header' => 'Account',
                ],
                [
                    'name' => 'Support',
                    'route' => 'support',
                    'icon' => 'heroicon-s-chat-bubble-left-ellipsis', // Heroicons solid
                ],
            ];
        } catch (\Exception $e) {
//            dd($e->getMessage());
            return [];
        }
    }

}
