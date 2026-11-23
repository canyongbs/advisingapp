<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Group\Filament\Resources\Groups\Pages;

use AdvisingApp\Group\Filament\Resources\Groups\GroupResource;
use AdvisingApp\Group\Filament\Resources\Groups\Tables\GroupPopulationMetricsStateGroup;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;

class ListGroups extends ListRecords
{
    protected ?string $heading = 'Student/Prospect Groups';

    protected static string $resource = GroupResource::class;

    public function table(Table $table): Table
    {
        $populationMetrics = GroupPopulationMetricsStateGroup::make();

        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('email_issues')
                    ->label('Email Issues')
                    ->lazyLoadedState(group: $populationMetrics),
                TextColumn::make('sms_issues')
                    ->label('SMS Issues')
                    ->lazyLoadedState(group: $populationMetrics),
                TextColumn::make('population_count')
                    ->label('Population Count')
                    ->numeric()
                    ->lazyLoadedState(group: $populationMetrics),
                TextColumn::make('model')
                    ->label('Population')
                    ->sortable()
                    ->visible(auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])),
                TextColumn::make('type')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable()
                    ->hidden(function (Table $table): bool {
                        $filter = $table->getFilter('my_groups');

                        // The column manager is initialized before the filter defaults are filled and query string values
                        // are normalized on the first request. Resolving the same visibility from the raw state avoids the
                        // column state changing on the next request, which would discard in-flight lazy-loaded state as stale.
                        return filter_var($filter->getState()['isActive'] ?? $filter->getDefaultState(), FILTER_VALIDATE_BOOLEAN);
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->authorize(function () {
                        return auth()->user()->can('delete');
                    })
                    ->action(function (DeleteAction $action, Group $record): void {
                        try {
                            $this->authorize('delete', $record);
                            // Let Filament handle the default success notification
                            $record->delete();
                        } catch (AuthorizationException $exception) {
                            $action->failureNotificationTitle($exception->getMessage())->sendFailureNotification();
                        }
                    }),
            ])
            ->filters([
                Filter::make('my_groups')
                    ->label('My Population Groups')
                    ->query(
                        fn ($query) => $query->where('user_id', auth()->id())
                    )
                    ->default(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
