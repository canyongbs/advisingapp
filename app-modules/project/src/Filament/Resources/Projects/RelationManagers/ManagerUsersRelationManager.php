<?php

namespace AdvisingApp\Project\Filament\Resources\Projects\RelationManagers;

use AdvisingApp\Project\Models\Project;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManagerUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'managerUsers';

    protected static ?string $title = 'Users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize('update', Project::class),
            ])
            ->recordActions([
                DetachAction::make()
                    ->authorize('update', Project::class),
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    ->authorize('update', Project::class),
            ])
            ->inverseRelationship('managedProjects');
    }
}
