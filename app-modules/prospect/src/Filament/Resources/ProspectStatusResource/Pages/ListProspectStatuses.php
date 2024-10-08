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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectStatusResource;

class ListProspectStatuses extends ListRecords
{
    protected static string $resource = ProspectStatusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classification')
                    ->label('Classification')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (ProspectStatus $prospectStatus) => $prospectStatus->color->value),
                TextColumn::make('prospects_count')
                    ->label('# of Prospects')
                    ->counts('prospects')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (DeleteBulkAction $component): void {
                            $total = 0;
                            $totalDeleted = 0;

                            $component->process(static function (Collection $records) use (&$total, &$totalDeleted) {
                                $total = $records->count();

                                $records->each(function (Model $record) use (&$totalDeleted) {
                                    try {
                                        $record->delete();

                                        $totalDeleted++;
                                    } catch (QueryException $e) {
                                        if (str_contains($e->getMessage(), 'Cannot modify system protected rows')) {
                                            Notification::make()
                                                ->title('Cannot Delete System Protected record')
                                                ->body('A system protected record cannot be deleted.')
                                                ->danger()
                                                ->send();
                                        }
                                    }
                                });
                            });

                            $notification = Notification::make()
                                ->title('Service Request Statuses Deleted')
                                ->body("{$totalDeleted} of {$total} selected service request statuses have been deleted.");

                            if ($totalDeleted > 0) {
                                $notification->success();
                            } else {
                                $notification->danger();
                            }

                            $notification->send();

                            if ($totalDeleted > 0) {
                                $component->dispatchSuccessRedirect();
                            } else {
                                $component->dispatchFailureRedirect();
                            }
                        }),
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
