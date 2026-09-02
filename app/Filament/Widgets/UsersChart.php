<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class UsersChart extends ChartWidget
{
    protected static ?string $heading = 'Users Summary';
    protected static ?string $description = 'This chart shows the number of users registered per month this year.';
    protected static ?int $sort = 3;
    protected static string $color = 'info';

    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data->map(fn($value) => $value->aggregate),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
