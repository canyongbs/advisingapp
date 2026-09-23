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

namespace AdvisingApp\Engagement\Livewire;

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Filament\Actions\BulkChangeStatusAction;
use AdvisingApp\Engagement\Filament\Pages\ViewEngagementResponse;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class InboxTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EngagementResponse::query()->with('latestActionedNote')
            )
            ->columns([
                TextColumn::make('direction')
                    ->state('Inbound')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->tooltip(fn (EngagementResponse $record): ?string => $record->status === EngagementResponseStatus::Actioned ? $record->latestActionedNote?->getActionedNoteTooltip() : null),
                TextColumn::make('sender_type')
                    ->label('Relation')
                    ->formatStateUsing(fn (EngagementResponse $record) => ucwords($record->sender_type))
                    ->sortable(),
                TextColumn::make('sender.full_name')
                    ->label('From')
                    ->url(fn (EngagementResponse $record): ?string => match (true) {
                        $record->sender instanceof Student => StudentResource::getViewUrl($record->sender),
                        $record->sender instanceof Prospect => ProspectResource::getUrl('view', ['record' => $record->sender]),
                        default => null,
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('type')
                    ->formatStateUsing(fn (EngagementResponse $record) => match ($record->type) {
                        EngagementResponseType::Email => 'Email',
                        EngagementResponseType::Sms => 'Text',
                    })
                    ->sortable(),
                TextColumn::make('subject')
                    ->formatStateUsing(function (EngagementResponse $record): ?string {
                        if ($record->type === EngagementResponseType::Email && filled($record->subject)) {
                            return $record->subject;
                        }

                        return filled($body = $record->getBody())
                            ? Str::limit(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 50)
                            : null;
                    })
                    ->description(function (EngagementResponse $record): ?string {
                        if ($record->type === EngagementResponseType::Email && filled($record->subject)) {
                            return filled($body = $record->getBody())
                                ? Str::limit(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 50)
                                : null;
                        }

                        return null;
                    })
                    ->searchable(['subject', 'content']),
                TextColumn::make('sent_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (EngagementResponse $record): string => ViewEngagementResponse::getUrl(['record' => $record])),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('sender.subscriptions.user', 'id', auth()->id())),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('sender.careTeam', 'user_id', auth()->id());
                        }
                    )
                    ->default(),
                SelectFilter::make('my_groups')
                    ->label('My Population Groups')
                    ->options(
                        auth()->user()->groups()
                            ->limit(20)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn (string $search): Collection => auth()->user()->groups()
                            ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                            ->limit(20)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn (string | int | null $value): ?string => filled($value)
                        ? auth()->user()->groups()->whereKey($value)->value('name')
                        : null)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('all_groups')
                    ->label('All Population Groups')
                    ->options(
                        Group::all()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn (string $search): Collection => Group::query()
                            ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                            ->limit(20)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn (string | int | null $value): ?string => filled($value)
                        ? Group::query()->whereKey($value)->value('name')
                        : null)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('status')
                    ->multiple()
                    ->label('Status')
                    ->options(EngagementResponseStatus::class)
                    ->searchable()
                    ->preload(),
                SelectFilter::make('sender_type')
                    ->label('Relation')
                    ->options([
                        'student' => 'Student',
                        'prospect' => 'Prospect',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        EngagementResponseType::Email->value => 'Email',
                        EngagementResponseType::Sms->value => 'Text',
                    ]),
            ])
            ->recordUrl(fn (EngagementResponse $record): string => ViewEngagementResponse::getUrl(['record' => $record]))
            ->defaultSort('sent_at', 'desc')
            ->emptyStateHeading('No Engagements yet.')
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkChangeStatusAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('engagement::livewire.unified-inbox-table');
    }

    /**
     * @param Builder<EngagementResponse> $query
     * @param array<string, mixed> $data
     */
    protected function groupFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $modelType = Group::find($data['value'])?->model;

        $query->whereHasMorph(
            'sender',
            [
                Student::class,
                Prospect::class,
            ],
            function (Builder $query, string $type) use ($data, $modelType): void {
                $shouldApplyFilter = match ($type) {
                    Student::class => $modelType === GroupModel::Student,
                    Prospect::class => $modelType === GroupModel::Prospect,
                    default => false,
                };

                if ($shouldApplyFilter) {
                    app(TranslateGroupFilters::class)
                        ->applyFilterToQuery($data['value'], $query);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        );
    }
}
