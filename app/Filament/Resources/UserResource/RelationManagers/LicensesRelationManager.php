<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Rules\LicenseTypeUsageRule;
use App\Filament\Resources\RelationManagers\RelationManager;

class LicensesRelationManager extends RelationManager
{
    protected static string $relationship = 'licenses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options(LicenseType::class)
                    ->enum(LicenseType::class)
                    ->rule(
                        Rule::unique('licenses', 'type')
                            ->where('user_id', $this->getOwnerRecord()->getKey())
                            ->whereNull('deleted_at'),
                    )
                    ->rule(new LicenseTypeUsageRule())
                    ->disableOptionWhen(function (string $value) {
                        /** @var User $ownerRecord */
                        $ownerRecord = $this->getOwnerRecord();

                        return ! LicenseType::from($value)->hasAvailableLicenses() || $ownerRecord->licenses()->where('type', $value)->exists();
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
