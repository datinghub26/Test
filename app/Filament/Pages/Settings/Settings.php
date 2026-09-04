<?php

namespace App\Filament\Pages\Settings;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 303;

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('Protection')
                        ->schema([
                            Section::make('Accounts')
                                ->schema([
                                    Checkbox::make('protection.enable_max_accounts_per_ip')
                                        ->helperText("Limit the number of accounts that can be created from the same IP address per a week."),
                                    TextInput::make('protection.max_accounts_per_ip')
                                        ->integer(),
                                ]),
                            Section::make('Change IP')
                                ->schema([
                                    Checkbox::make('protection.ip_change_enable')
                                        ->label('Enable IP Change Protection')
                                        ->helperText("Prevent users from changing their IP address."),
                                    Checkbox::make('protection.ip_change_block_acc')
                                        ->label('Auto Block Account')
                                        ->helperText("Block the account if the user changes their IP address."),
                                ]),
                            Section::make('Email')
                                ->schema([
                                    Checkbox::make('protection.email_custom_domain')
                                        ->label('Custom Domain')
                                        ->helperText("Allow users to use only a specific domain for their email address."),
                                    TagsInput::make('protection.email_custom_domain_value')
                                        ->suggestions(['gmail.com', 'yahoo.com', 'hotmail.com'])
                                        ->splitKeys(['Tab', ' ', ','])
                                        ->label('Domains')
                                        ->placeholder('gmail.com,yahoo.com')
                                        ->helperText("Comma separated list of domains."),
                                    Checkbox::make('protection.email_verify_to_withdraw')
                                        ->label('Verify Email to Withdraw')
                                        ->helperText("Require users to verify their email address before they can withdraw money."),
                                ]),
                            Section::make('Country')
                                ->schema([
                                    Checkbox::make('protection.country_block')
                                        ->label('Block Countries')
                                        ->helperText("Block users from specific countries."),
                                    TagsInput::make('protection.country_block_value')
                                        ->suggestions(['US', 'CA', 'UK'])
                                        ->splitKeys(['Tab', ' ', ','])
                                        ->label('Countries')
                                        ->placeholder('US,CA,UK')
                                        ->helperText("Comma separated list of country codes."),
                                ]),
                            Section::make('IPQualityScore')
                                ->description("IPQualityScore provides the most accurate IP address scoring and threat analysis.")
                                ->headerActions([
                                    Action::make('help')
                                        ->label('Help')
                                        ->color('secondary')
                                        ->icon('heroicon-o-information-circle')
                                        ->url('https://www.ipqualityscore.com/', true),
                                ])
                                ->schema([
                                    Checkbox::make('protection.ipqs_enable')
                                        ->label('Enable IPQualityScore'),
                                    TextInput::make('protection.ipqs_api_key')
                                        ->label('API Key'),
                                    TextInput::make('protection.ipqs_risk')
                                        ->label('Risk Threshold')
                                        ->placeholder('0-100')
                                        ->helperText("The risk score threshold to block the user."),
                                ]),
                            Section::make('ProxyCheck.io')
                                ->description("ProxyCheck.io provides a real-time proxy checking service.")
                                ->headerActions([
                                    Action::make('help')
                                        ->label('Help')
                                        ->color('secondary')
                                        ->icon('heroicon-o-information-circle')
                                        ->url('https://proxycheck.io/', true),
                                ])
                                ->schema([
                                    Checkbox::make('protection.proxycheck_enable')
                                        ->label('Enable ProxyCheck'),
                                    TextInput::make('protection.proxycheck_api_key')
                                        ->label('API Key'),
                                ]),


                        ]),
                    Tabs\Tab::make('Postback')
                        ->schema([
                            Section::make('Lead Configuration')
                                ->schema([
                                    Checkbox::make('postback.enable_pending')
                                        ->label('Enable Pending Leads')
                                        ->helperText('Toggle to activate postback for pending leads.'),
                                    TextInput::make('postback.pending_threshold')
                                        ->numeric()
                                        ->label('Pending Points Threshold')
                                        ->placeholder('5000')
                                        ->helperText('Leads with points exceeding this value will be marked as pending.'),
                                ]),
                        ]),
                    Tabs\Tab::make('Third Party')
                        ->schema([
                            Section::make('Google OAuth')
                                ->headerActions([
                                    Action::make('help')
                                        ->label('Help')
                                        ->color('secondary')
                                        ->icon('heroicon-o-information-circle')
                                        ->url('https://www.youtube.com/watch?v=-vq32dsK_TI', true),
                                ])
                                ->schema([
                                    Checkbox::make('services.google.oauth_enable')
                                        ->label('Enable Google OAuth'),
                                    TextInput::make('services.google.client_id')
                                        ->label('Client ID'),
                                    TextInput::make('services.google.client_secret')
                                        ->label('Client Secret'),
                                ]),
                            Section::make('Google Analytics')
                                ->schema([
                                    Checkbox::make('services.google.analytics_enable')
                                        ->label('Enable Google Analytics'),
                                    TextInput::make('services.google.analytics_id')
                                        ->label('Tracking ID'),
                                ]),
                        ]),
                    Tabs\Tab::make('Referral')
                        ->schema([
                            Checkbox::make('referral.enable_rewards'),
                            TextInput::make('referral.points')
                                ->helperText("Points to be awarded to the referrer when a referred user signs up."),
                        ]),
                    Tabs\Tab::make('Levels')
                        ->schema([
                            Checkbox::make('levels.enable_rewards'),
                            TextInput::make('levels.exp_per_dollar')
                                ->label('Experience Points per Dollar')
                                ->helperText("Experience points to be awarded to the user when they make a purchase."),

                        ]),
                    Tabs\Tab::make('Leaderboard')
                        ->schema([
                            Checkbox::make('leaderboard.enable_rewards'),
                            TextInput::make('leaderboard.restart_interval')
                                ->suffix('Days')
                                ->integer()
                                ->label('Restart Interval')
                                ->helperText("Number of days after which the leaderboard will be reset.")

                        ]),
                    Tabs\Tab::make('Streaks')
                        ->schema([
                            Checkbox::make('streaks.enable_rewards'),
                            TextInput::make('streaks.required_points')
                                ->suffix('Points')
                                ->integer()
                                ->label('Required Points')
                                ->helperText("Number of points required to maintain the streak."),
                            TextInput::make('streaks.required_hours')
                                ->suffix('Hours')
                                ->integer()
                                ->label('Required Hours')
                                ->helperText("Number of hours required to maintain the streak.")
                        ]),
                    Tabs\Tab::make('Social Media')
                        ->schema([
                            TextInput::make('social.linkedin')
                                ->label('LinkedIn'),
                            TextInput::make('social.facebook')
                                ->label('Facebook'),
                            TextInput::make('social.telegram')
                                ->label('Telegram'),
                            TextInput::make('social.twitter')
                                ->label('Twitter'),
                            TextInput::make('social.instagram')
                                ->label('Instagram'),
                            TextInput::make('social.trustpilot')
                                ->label('Trust pilot'),
                            TextInput::make('social.reddit')
                                ->label('Reddit'),
                            TextInput::make('social.discord')
                                ->label('Discord'),
                        ]),
                    Tabs\Tab::make('Notification Tone')
                        ->schema([
                            Section::make('Notification Tone Settings')
                                ->description('Configure or upload sound alerts for offer completions and user balance updates.')
                                ->schema([
                                    FileUpload::make('general.notification_sound')
                                        ->label('Offer Completion Audio File (.mp3, .wav)')
                                        ->disk('public')
                                        ->directory('sounds')
                                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/x-wav'])
                                        ->maxSize(10240)
                                        ->helperText('Upload a custom audio notification that will play for users when an offer is completed and points are credited. If empty, the default tone at public/assets/sounds/notification.mp3 is used.'),
                                ]),
                        ]),
                ])
                ->persistTabInQueryString(),
        ];
    }
}
