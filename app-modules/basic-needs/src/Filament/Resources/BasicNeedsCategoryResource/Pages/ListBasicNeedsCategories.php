<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource;
use App\Exceptions\SoftDeleteContraintViolationException;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class ListBasicNeedsCategories extends ListRecords
{
    protected static string $resource = BasicNeedsCategoryResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function (Collection $records) {
                            try {
                                $records->each->delete();
                                return true;
                            } catch (SoftDeleteContraintViolationException $e) {
                                Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                                return false;
                            }
                        })
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
