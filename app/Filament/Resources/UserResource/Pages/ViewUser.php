<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use DeviceDetector\DeviceDetector;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Personal Information')
                ->columns(4)
                ->collapsed()
                ->compact()
                ->schema([
                    TextEntry::make('username'),
                    TextEntry::make('email'),
                    TextEntry::make('email_verified_at')
                        ->dateTime(),
                    IconEntry::make('privacy')
                        ->boolean(),
                    TextEntry::make('role')
                        ->badge()
                        ->color(fn($record) => match ($record->role) {
                            'admin' => 'danger',
                            'user' => 'success',
                        }),
                    TextEntry::make('level')
                        ->formatStateUsing(fn($state) => "Level $state")
                        ->badge(),
                    TextEntry::make('points')
                        ->numeric(),
                    TextEntry::make('geo.country_code')
                        ->formatStateUsing(fn(string $state): View => view(
                            'filament.flag', ['state' => $state],
                        ))
                        ->label('Country'),
                ]),

            Section::make('Geo Information')
                ->relationship('geo')
                ->columns(3)
                ->collapsed()
                ->compact()
                ->schema([
                    TextEntry::make('ip'),
                    TextEntry::make('country_code')
                        ->formatStateUsing(fn(string $state): View => view(
                            'filament.flag', ['state' => $state],
                        )),
                    TextEntry::make('user_agent')
                        ->formatStateUsing(function (string $state): View {
                            $dd = new DeviceDetector($state);
                            $dd->parse();
                            return view('filament.device', ['dd' => $dd, 'state' => $state, 'showBrand' => true]);
                        }),
                    TextEntry::make('latest_login_ip'),
                    TextEntry::make('latest_login_at')
                        ->dateTime(),
                    TextEntry::make('latest_user_agent')
                        ->formatStateUsing(function (string $state): View {
                            $dd = new DeviceDetector($state);
                            $dd->parse();
                            return view('filament.device', ['dd' => $dd, 'state' => $state, 'showBrand' => true]);
                        }),
                ]),
        ]);
    }
}
