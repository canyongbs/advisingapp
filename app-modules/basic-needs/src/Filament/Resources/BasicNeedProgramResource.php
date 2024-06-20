<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\BasicNeeds\Models\BasicNeedProgram;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedProgramResource\Pages;

class BasicNeedProgramResource extends Resource
{
    protected static ?string $model = BasicNeedProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Program';

    protected static ?string $modelLabel = 'Program';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Basic Needs';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBasicNeedPrograms::route('/'),
            'create' => Pages\CreateBasicNeedProgram::route('/create'),
            'view' => Pages\ViewBasicNeedProgram::route('/{record}'),
            'edit' => Pages\EditBasicNeedProgram::route('/{record}/edit'),
        ];
    }
}
