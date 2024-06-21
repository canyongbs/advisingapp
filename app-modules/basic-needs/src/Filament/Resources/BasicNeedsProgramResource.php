<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources;

use Laravel\Pennant\Feature;
use Filament\Resources\Resource;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\EditBasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\ViewBasicNeedsProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\ListBasicNeedsPrograms;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages\CreateBasicNeedsProgram;

class BasicNeedsProgramResource extends Resource
{
    protected static ?string $model = BasicNeedsProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Program';

    protected static ?string $modelLabel = 'Program';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Basic Needs';

    public static function canAccess(): bool
    {
        if (Feature::active('basic-needs')) {
            return parent::canAccess();
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBasicNeedsPrograms::route('/'),
            'create' => CreateBasicNeedsProgram::route('/create'),
            'view' => ViewBasicNeedsProgram::route('/{record}'),
            'edit' => EditBasicNeedsProgram::route('/{record}/edit'),
        ];
    }
}
