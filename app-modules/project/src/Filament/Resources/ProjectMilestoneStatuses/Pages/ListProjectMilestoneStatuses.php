<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatuses\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatuses\ProjectMilestoneStatusResource;
use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ListProjectMilestoneStatuses extends ListRecords
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('project_milestones_count')
                    ->label('Usage Count')
                    ->counts('milestones')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletedCount = ProjectMilestoneStatus::query()
                                ->whereKey($records)
                                ->whereDoesntHave('milestones')
                                ->delete();

                            Notification::make()
                                ->title('Deleted ' . $deletedCount . ' statuses')
                                ->body(
                                    $deletedCount < $records->count()
                                        ? ($records->count() - $deletedCount) . ' statuses were not deleted because they have associated project milestones.'
                                        : null
                                )
                                ->success()
                                ->send();
                        })
                        ->fetchSelectedRecords(false),
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
