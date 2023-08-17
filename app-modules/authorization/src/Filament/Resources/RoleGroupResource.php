<?php

namespace Assist\Authorization\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Assist\Authorization\Models\RoleGroup;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Assist\Authorization\Filament\Resources\RoleGroupResource\Pages\EditRoleGroup;
use Assist\Authorization\Filament\Resources\RoleGroupResource\Pages\ViewRoleGroup;
use Assist\Authorization\Filament\Resources\RoleGroupResource\Pages\ListRoleGroups;
use Assist\Authorization\Filament\Resources\RoleGroupResource\Pages\CreateRoleGroup;
use Assist\Authorization\Filament\Resources\RoleGroupResource\RelationManagers\RolesRelationManager;
use Assist\Authorization\Filament\Resources\RoleGroupResource\RelationManagers\UsersRelationManager;
use Assist\Authorization\Filament\Resources\RoleGroupResource\RelationManagers\PermissionsRelationManager;

class RoleGroupResource extends Resource
{
    protected static ?string $model = RoleGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(125),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
            UsersRelationManager::class,
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoleGroups::route('/'),
            'create' => CreateRoleGroup::route('/create'),
            'view' => ViewRoleGroup::route('/{record}'),
            'edit' => EditRoleGroup::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
