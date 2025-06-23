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

class ManageCaseTypeAuditors extends ManageRelatedRecords
{
    protected static string $resource = CaseTypeResource::class;

    protected static string $relationship = 'auditors';

    public static function getNavigationLabel(): string
    {
        return 'Auditors';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && CaseTypeManagerAuditor::active();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('auditableCaseTypes')
            ->columns([
                TextColumn::make('name'),
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
