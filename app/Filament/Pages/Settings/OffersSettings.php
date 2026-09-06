<?php

namespace App\Filament\Pages\Settings;

use App\Console\Commands\UpdateOffers;
use Artisan;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class OffersSettings extends BaseSettings
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Config';
    protected static ?string $navigationGroup = 'Offers';

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? 'Config';
    }

    public function getTitle(): string
    {
        return static::$navigationLabel ?? 'Config';
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Offers')
                ->schema([
                    Tabs\Tab::make('Top Offers')
                        ->schema([
                            \Filament\Forms\Components\Select::make('top_offers.mode')
                                ->label('Top Offers Mode')
                                ->options([
                                    'hybrid' => 'Hybrid (Manual Offers First + Auto Remaining)',
                                    'auto' => 'Automatic (Highest Performance Data)',
                                    'manual' => 'Manual (Only Admin Selected Offers)',
                                ])
                                ->default('hybrid')
                                ->helperText('Choose whether Top Offers are chosen automatically, manually by admin, or hybrid.'),
                            TextInput::make('top_offers.limit')
                                ->label('Maximum Offers Displayed')
                                ->numeric()
                                ->default(6)
                                ->helperText('The maximum number of offers shown in the Top Offers section.'),
                            \Filament\Forms\Components\Select::make('top_offers.ranking_metric')
                                ->label('Automatic Ranking Metric')
                                ->options([
                                    'conversions' => 'Most Completed (Conversions)',
                                    'points' => 'Highest ERC Reward',
                                    'payout' => 'Highest Publisher Payout',
                                ])
                                ->default('conversions')
                                ->helperText('The metric used to rank top converting offers in automatic or hybrid mode.'),
                        ]),
                    Tabs\Tab::make('AdGate')
                        ->schema([
                            Checkbox::make('offers.adgatemedia.enabled')
                                ->default(false),
                            TextInput::make('offers.adgatemedia.affiliate_id')
                                ->label('Affiliate ID'),
                            TextInput::make('offers.adgatemedia.api_key')
                                ->label('API Key'),
                            TextInput::make('offers.adgatemedia.wall_code')
                                ->label('Wall Code'),
                        ]),
                    Tabs\Tab::make('Admantum')
                        ->schema([
                            Checkbox::make('offers.admantum.enabled')
                                ->default(false),
                            TextInput::make('offers.admantum.appid')
                                ->label('App ID'),
                        ]),
                    Tabs\Tab::make('Torox')
                        ->schema([
                            Checkbox::make('offers.torox.enabled')
                                ->default(false),
                            TextInput::make('offers.torox.pubid')
                                ->label('Pub ID'),
                            TextInput::make('offers.torox.appid')
                                ->label('App ID'),
                            TextInput::make('offers.torox.secretkey')
                                ->label('Secret Key'),
                        ]),
                    Tabs\Tab::make('Monlix')
                        ->schema([
                            Checkbox::make('offers.monlix.enabled')
                                ->default(false),
                            TextInput::make('offers.monlix.api_key')
                                ->label('API Key'),
                            TextInput::make('offers.monlix.secret_key')
                                ->label('Secret Key'),
                            TextInput::make('offers.monlix.payout_rate')
                                ->numeric()
                                ->label('Payout Rate'),
                        ]),
                    Tabs\Tab::make('Adscendmedia')
                        ->schema([
                            Checkbox::make('offers.adscendmedia.enabled')
                                ->default(false),
                            TextInput::make('offers.adscendmedia.pub_id')
                                ->label('Pub ID'),
                            TextInput::make('offers.adscendmedia.api_key')
                                ->label('API Key'),
                            TextInput::make('offers.adscendmedia.payout_rate')
                                ->numeric()
                                ->label('Payout Rate'),
                        ]),
                    Tabs\Tab::make('Ayet Studios')
                        ->schema([
                            Placeholder::make('Ayet Studios')
                                ->content(new HtmlString('<p class="text-sm text-gray-500">Coming soon...</p>'))
                        ]),
                    Tabs\Tab::make('Notik')
                        ->schema([
                            Checkbox::make('offers.notik.enabled')
                                ->default(false),
                            TextInput::make('offers.notik.api_key')
                                ->label('API Key'),
                            TextInput::make('offers.notik.pub_id')
                                ->label('Pub ID'),
                            TextInput::make('offers.notik.app_id')
                                ->label('App ID'),
                            TextInput::make('offers.notik.payout_rate')
                                ->numeric()
                                ->label('Payout Rate'),
                        ]),
                    Tabs\Tab::make('CPAGrip')
                        ->schema([
                            Checkbox::make('offers.cpagrip.enabled')
                                ->label('Enable CPAGrip Offers')
                                ->default(false),
                            TextInput::make('offers.cpagrip.user_id')
                                ->label('CPAGrip Account User ID'),
                            TextInput::make('offers.cpagrip.pubkey')
                                ->label('CPAGrip API Public Key'),
                            TextInput::make('offers.cpagrip.payout_rate')
                                ->numeric()
                                ->default(500)
                                ->label('Payout Rate (Points per $1 USD)')
                                ->helperText('Example: 500 means $1.00 USD = 500 ERC'),
                        ]),
                    Tabs\Tab::make('ClickWall')
                        ->schema([
                            Checkbox::make('offers.clickwall.enabled')
                                ->label('Enable ClickWall')
                                ->default(false),
                            TextInput::make('offers.clickwall.app_id')
                                ->label('ClickWall App ID'),
                            TextInput::make('offers.clickwall.api_key')
                                ->label('API / Secret Key'),
                            TextInput::make('offers.clickwall.payout_rate')
                                ->numeric()
                                ->default(500)
                                ->label('Payout Rate (Points per $1 USD)')
                                ->helperText('Example: 500 means $1.00 USD = 500 ERC'),
                        ]),
                ]),
        ];
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Updating Offers ...')
            ->body('Updating offers will take effect in a few minutes.')
            ->warning()
            ->send();

        Artisan::call(UpdateOffers::class);
    }


}
