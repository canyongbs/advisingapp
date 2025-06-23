<?php

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages;

use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use App\Features\CaseTypeManagerAuditor;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageCaseTypeManagers extends ManageRelatedRecords
{
    protected static string $resource = CaseTypeResource::class;

    protected static string $relationship = 'managers';

    public static function getNavigationLabel(): string
    {
        return 'Managers';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && CaseTypeManagerAuditor::active();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('manageableCaseTypes')
            ->columns([
                TextColumn::make('name')
                    ->label('Team'),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
