<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages;

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource;
use App\Exceptions\SoftDeleteContraintViolationException;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
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
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-s-trash')
                        ->label('Delete selected')
                        ->modalHeading('Delete selected categories')
                        ->modalIcon('heroicon-o-trash')
                        ->action(function (Collection $records) {
                            $successfullyDeleted = 0;
                            $unsuccessfullyDeleted = 0;

                            foreach ($records as $record) {
                                try {
                                    $record->delete();
                                    $successfullyDeleted++;
                                } catch (SoftDeleteContraintViolationException $e) {
                                    $unsuccessfullyDeleted++;
                                }
                            }

                            if ($successfullyDeleted > 0) {
                                Notification::make()
                                    ->title(__(':count categories has been deleted.', ['count' => $successfullyDeleted]))
                                    ->success()
                                    ->send();
                            }

                            if ($unsuccessfullyDeleted > 0) {
                                Notification::make()
                                    ->title(__(':count categories cannot be deleted due to linked with programs.', ['count' => $unsuccessfullyDeleted]))
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
