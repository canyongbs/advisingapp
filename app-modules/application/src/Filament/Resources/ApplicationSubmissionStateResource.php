<?php

namespace AdvisingApp\Application\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages\EditApplicationSubmissionState;
use AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages\ViewApplicationSubmissionState;
use AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages\ListApplicationSubmissionStates;
use AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages\CreateApplicationSubmissionState;

class ApplicationSubmissionStateResource extends Resource
{
    protected static ?string $model = ApplicationSubmissionState::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 18;

    public static function getPages(): array
    {
        return [
            'index' => ListApplicationSubmissionStates::route('/'),
            'create' => CreateApplicationSubmissionState::route('/create'),
            'view' => ViewApplicationSubmissionState::route('/{record}'),
            'edit' => EditApplicationSubmissionState::route('/{record}/edit'),
        ];
    }
}
