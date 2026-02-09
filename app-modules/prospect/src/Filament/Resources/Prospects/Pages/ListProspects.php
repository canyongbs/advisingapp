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

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Pages;

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
use AdvisingApp\Prospect\Filament\Actions\ProspectTagsBulkAction;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Imports\ProspectImporter;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Enums\CareTeamRoleType;
use App\Enums\TagType;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Tag;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make(Prospect::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryEmailAddress.address')
                    ->label('Primary Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryPhoneNumber.number')
                    ->label('Primary Phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (Prospect $record) => $record->status->color->value)
                    ->toggleable()
                    ->sortable(['sort']),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('my_groups')
                    ->label('My Population Groups')
                    ->options(
                        auth()->user()->groups()
                            ->where('model', GroupModel::Prospect)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('all_groups')
                    ->label('All Population Groups')
                    ->options(
                        Group::all()
                            ->where('model', GroupModel::Prospect)
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
                SelectFilter::make('status_id')
                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('sort'))
                    ->multiple()
                    ->preload(),
                SelectFilter::make('source_id')
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('tags')
                    ->label('Tags')
                    ->options(fn (): array => Tag::query()->where('type', TagType::Prospect)->pluck('name', 'id')->toArray())
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
            ], layout: FiltersLayout::BeforeContent)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SubscribeTableAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ActionGroup::make([
                        BulkAction::make('bulk_update')
                            ->label('Update Records')
                            ->icon('heroicon-o-pencil-square')
                            ->form([
                                Select::make('field')
                                    ->options([
                                        'description' => 'Description',
                                        'email_bounce' => 'Email Bounce',
                                        'hsgrad' => 'High School Graduation Year',
                                        'source_id' => 'Source',
                                        'status_id' => 'Status',
                                    ])
                                    ->required()
                                    ->live(),
                                Textarea::make('description')
                                    ->string()
                                    ->required()
                                    ->visible(fn (Get $get) => $get('field') === 'description'),
                                Select::make('email_bounce')
                                    ->label('Email Bounce')
                                    ->boolean()
                                    ->visible(fn (Get $get) => $get('field') === 'email_bounce'),
                                TextInput::make('hsgrad')
                                    ->label('High School Graduation Year')
                                    ->numeric()
                                    ->minValue(1920)
                                    ->maxValue(now()->addYears(25)->year)
                                    ->required()
                                    ->visible(fn (Get $get) => $get('field') === 'hsgrad'),
                                Select::make('source_id')
                                    ->label('Source')
                                    ->relationship('source', 'name')
                                    ->exists(
                                        table: (new ProspectSource())->getTable(),
                                        column: (new ProspectSource())->getKeyName()
                                    )
                                    ->required()
                                    ->visible(fn (Get $get) => $get('field') === 'source_id'),
                                Select::make('status_id')
                                    ->label('Status')
                                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('sort'))
                                    ->exists(
                                        table: (new ProspectStatus())->getTable(),
                                        column: (new ProspectStatus())->getKeyName()
                                    )
                                    ->required()
                                    ->visible(fn (Get $get) => $get('field') === 'status_id'),
                            ])
                            ->action(function (Collection $records, array $data) {
                                $records->each(
                                    fn (Prospect $prospect) => $prospect
                                        ->forceFill([$data['field'] => $data[$data['field']]])
                                        ->save()
                                );

                                Notification::make()
                                    ->title($records->count() . ' ' . str('Prospect')->plural($records->count()) . ' Updated')
                                    ->success()
                                    ->send();
                            }),
                    ])->dropdown(false),
                    ActionGroup::make([
                        SubscribeBulkAction::make(context: 'prospect')->authorize(fn (): bool => auth()->user()->can('prospect.*.update')),
                        AddCareTeamMemberAction::make(CareTeamRoleType::Prospect),
                        ProspectTagsBulkAction::make()->visible(fn (): bool => auth()->user()->can('prospect.*.update')),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkEmailAction::make(context: 'prospects')->authorize(fn () => Gate::allows('update', [auth()->user(), Prospect::class])),
                        BulkTextAction::make(context: 'prospects')->authorize(fn () => Gate::allows('update', [auth()->user(), Prospect::class])),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkCreateCaseAction::make()
                            ->authorize(fn () => auth()->user()->can('prospect.*.update')),
                        BulkCreateConcernAction::make()
                            ->visible(fn (): bool => auth()->user()->can('prospect.*.update')),
                        BulkCreateInteractionAction::make()
                            ->authorize(fn () => auth()->user()->can('prospect.*.update')),
                    ])->dropdown(false),
                    ActionGroup::make([
                        BulkGroupAction::make(groupModel: GroupModel::Prospect),
                    ])->dropdown(false),
                    ActionGroup::make([
                        DeleteBulkAction::make()->label('Delete'),
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
            ImportAction::make()
                ->importer(ProspectImporter::class)
                ->authorize('import', Prospect::class),
            CreateAction::make(),
        ];
    }
}
