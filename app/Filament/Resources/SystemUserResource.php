<?php

namespace App\Filament\Resources;

use App\Models\SystemUser;
use Filament\Resources\Resource;
use App\Filament\Resources\SystemUserResource\Pages\EditSystemUser;
use App\Filament\Resources\SystemUserResource\Pages\ListSystemUsers;
use App\Filament\Resources\SystemUserResource\Pages\CreateSystemUser;
use App\Filament\Resources\SystemUserResource\RelationManagers\PermissionsRelationManager;

class SystemUserResource extends Resource
{
    protected static ?string $model = SystemUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users and Permissions';

    protected static ?int $navigationSort = 7;

    public static function getRelations(): array
    {
        return [
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemUsers::route('/'),
            'create' => CreateSystemUser::route('/create'),
            'edit' => EditSystemUser::route('/{record}/edit'),
        ];
    }
}
