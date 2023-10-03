<?php

namespace Assist\Team\Filament\Resources;

use Assist\Team\Models\Team;
use Filament\Resources\Resource;
use Assist\Team\Filament\Resources\TeamResource\Pages\EditTeam;
use Assist\Team\Filament\Resources\TeamResource\Pages\ViewTeam;
use Assist\Team\Filament\Resources\TeamResource\Pages\ListTeams;
use Assist\Team\Filament\Resources\TeamResource\Pages\CreateTeam;
use Assist\Team\Filament\Resources\TeamResource\RelationManagers\UsersRelationManager;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users and Permissions';

    protected static ?int $navigationSort = 5;

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'view' => ViewTeam::route('/{record}'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
