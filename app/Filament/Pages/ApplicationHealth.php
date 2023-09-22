<?php

namespace App\Filament\Pages;

use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults;

class ApplicationHealth extends HealthCheckResults
{
    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 7;

    public static function getNavigationBadge(): ?string
    {
        $count = app(ResultStore::class)
            ->latestResults()
            ?->storedCheckResults
            ->filter(fn ($check) => $check->status !== Status::ok()->value)
            ->count();

        return $count > 0 ? $count : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
    }
}
