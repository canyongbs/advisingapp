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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use AdvisingApp\Segment\Models\Segment;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use AdvisingApp\Segment\Enums\SegmentModel;
use Filament\Tables\Actions\BulkActionGroup;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;
use AdvisingApp\Engagement\Filament\Actions\BulkEngagementAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeBulkAction;
use AdvisingApp\CareTeam\Filament\Actions\ToggleCareTeamBulkAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeTableAction;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\Engagement\Filament\Actions\Contracts\HasBulkEngagementAction;
use AdvisingApp\Engagement\Filament\Actions\Concerns\ImplementsHasBulkEngagementAction;

class ListStudents extends ListRecords implements HasBulkEngagementAction
{
    use ImplementsHasBulkEngagementAction;

    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('sisid')
                    ->label('SIS ID')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->label('Other ID')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('my_segments')
                    ->label('My Population Segments')
                    ->options(
                        auth()->user()->segments()
                            ->where('model', SegmentModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->segmentFilter($query, $data)),
                SelectFilter::make('all_segments')
                    ->label('All Population Segments')
                    ->options(
                        Segment::all()
                            ->where('model', SegmentModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->segmentFilter($query, $data)),
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
                TernaryFilter::make('sap')
                    ->label('SAP'),
                TernaryFilter::make('dual'),
                TernaryFilter::make('ferpa')
                    ->label('FERPA'),
                Filter::make('holds')
                    ->form([
                        TextInput::make('hold'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hold'],
                                fn (Builder $query, $hold): Builder => $query->where('holds', 'ilike', "%{$hold}%"),
                            );
                    }),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('careTeam', 'user_id', '=', auth()->id())
                                ->get();
                        }
                    ),
            ])
            ->actions([
                ViewAction::make()
                    ->visible(function (Student $record) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->can('student_record_manager.*.view');
                    }),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'students'),
                    ToggleCareTeamBulkAction::make(),
                ]),
            ]);
    }

    protected function segmentFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $query->whereKey(
            app(TranslateSegmentFilters::class)
                ->handle($data['value'])
                ->pluck($query->getModel()->getQualifiedKeyName()),
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
