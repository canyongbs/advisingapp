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

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages;

use AdvisingApp\CareTeam\Filament\Actions\AddCareTeamMemberAction;
use AdvisingApp\CaseManagement\Filament\Actions\BulkCreateCaseAction;
use AdvisingApp\Concern\Filament\Actions\BulkCreateConcernAction;
use AdvisingApp\Engagement\Filament\Actions\BulkEmailAction;
use AdvisingApp\Engagement\Filament\Actions\BulkTextAction;
use AdvisingApp\Group\Actions\BulkGroupAction;
use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Filament\Actions\BulkCreateInteractionAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeBulkAction;
use AdvisingApp\Notification\Filament\Actions\SubscribeTableAction;
use AdvisingApp\StudentDataModel\Actions\DeleteStudent;
use AdvisingApp\StudentDataModel\Filament\Actions\StudentTagsBulkAction;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Enums\TagType;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Institutional Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryPhoneNumber.number')
                    ->label('Primary Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sisid')
                    ->label('SIS ID')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->label('Other ID')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('my_groups')
                    ->label('My Population Groups')
                    ->options(
                        auth()->user()->groups()
                            ->where('model', GroupModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('all_groups')
                    ->label('All Population Groups')
                    ->options(
                        Group::all()
                            ->where('model', GroupModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('careTeam', 'user_id', '=', auth()->id())
                                ->get();
                        }
                    ),
                SelectFilter::make('concerns')
                    ->multiple()
                    ->relationship('concerns.status', 'name')
                    ->searchable()
                    ->preload()
                    ->optionsLimit(20),
                SelectFilter::make('sis_category')
                    ->label('SIS Category')
                    ->options(fn (): array => Student::query()
                        ->whereNotNull('sis_category')
                        ->distinct()
                        ->orderBy('sis_category')
                        ->limit(50)
                        ->pluck('sis_category', 'sis_category')
                        ->all()),
                TernaryFilter::make('sap')
                    ->label('SAP'),
                TernaryFilter::make('dual'),
                TernaryFilter::make('ferpa')
                    ->label('FERPA'),
                Filter::make('holds')
                    ->schema([
                        TextInput::make('hold'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hold'],
                                fn (Builder $query, $hold): Builder => $query->where('holds', 'ilike', "%{$hold}%"),
                            );
                    }),

                SelectFilter::make('tags')
                    ->label('Tags')
                    ->options(fn (): array => Tag::query()->where('type', TagType::Student)->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->preload()
                    ->optionsLimit(20)
                    ->multiple()
                    ->query(
                        function (Builder $query, array $data) {
                            if (blank($data['values'])) {
                                return;
                            }

                            $query->whereHas('tags', function (Builder $query) use ($data) {
                                $query->whereIn('tag_id', $data['values']);
                            });
                        }
                    ),
                TernaryFilter::make('firstgen')
                    ->label('First Generation'),
            ], layout: FiltersLayout::BeforeContent)
            ->recordActions([
                ViewAction::make()
                    ->visible(function (Student $record) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->can('product_admin.*.view');
                    }),
                SubscribeTableAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ActionGroup::make([
                        SubscribeBulkAction::make(context: 'student')->authorize(fn (): bool => auth()->user()->can('student.*.update')),
                        AddCareTeamMemberAction::make(CareTeamRoleType::Student),
                        StudentTagsBulkAction::make()->visible(fn (): bool => auth()->user()->can('student.*.update')),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkEmailAction::make(context: 'students')->authorize(fn () => Gate::allows('update', [auth()->user(), Student::class])),
                        BulkTextAction::make(context: 'students')->authorize(fn () => Gate::allows('update', [auth()->user(), Student::class])),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkCreateCaseAction::make()
                            ->authorize(fn () => auth()->user()->can('student.*.update')),
                        BulkCreateConcernAction::make()
                            ->visible(fn (): bool => auth()->user()->can('student.*.update')),
                        BulkCreateInteractionAction::make()
                            ->authorize(fn () => auth()->user()->can('student.*.update')),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkGroupAction::make(groupModel: GroupModel::Student),
                    ])->dropdown(false),
                    ActionGroup::make([
                        DeleteBulkAction::make()
                            ->label('Delete')
                            ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
                            ->action(function (Collection $records) {
                                $deletedCount = 0;
                                $notDeleteCount = 0;

                                /** @var Collection|Student[] $records */
                                foreach ($records as $record) {
                                    /** @var Student $record */
                                    $response = Gate::inspect('delete', $record);

                                    if ($response->allowed()) {
                                        app(DeleteStudent::class)->execute($record);
                                        $deletedCount++;
                                    } else {
                                        $notDeleteCount++;
                                    }
                                }

                                $wasWere = fn ($count) => $count === 1 ? 'was' : 'were';

                                $notification = match (true) {
                                    $deletedCount === 0 => [
                                        'title' => 'None deleted',
                                        'status' => 'danger',
                                        'body' => "{$notDeleteCount} {$wasWere($notDeleteCount)} skipped because you do not have permission to delete.",
                                    ],
                                    $deletedCount > 0 && $notDeleteCount > 0 => [
                                        'title' => 'Some deleted',
                                        'status' => 'warning',
                                        'body' => "{$deletedCount} {$wasWere($deletedCount)} deleted, but {$notDeleteCount} {$wasWere($notDeleteCount)} skipped because you do not have permission to delete.",
                                    ],
                                    default => [
                                        'title' => 'Deleted',
                                        'status' => 'success',
                                        'body' => null,
                                    ],
                                };

                                Notification::make()
                                    ->title($notification['title'])
                                    ->{$notification['status']}()
                                    ->body($notification['body'])
                                    ->send();
                            }),
                    ])->dropdown(false),
                ]),
            ]);
    }

    protected function groupFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $query->whereKey(
            app(TranslateGroupFilters::class)
                ->execute($data['value'])
                ->pluck($query->getModel()->getQualifiedKeyName()),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
