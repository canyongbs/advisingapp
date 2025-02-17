<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Alert\Filament\Resources\AlertResource\Pages;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Filament\Resources\AlertResource;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Scopes\EducatableSearch;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Forms\Components\EducatableSelect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    // TODO: Change this to a link to the students page when tableAction link triggering becomes available in Filament 3.1
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    }),
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status.name'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    })
                    ->searchable(query: fn (Builder $query, $search) => $query->tap(new EducatableSearch(relationship: 'concern', search: $search)))
                    ->forceSearchCaseInsensitive()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable(),
                TextColumn::make('status.name')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', auth()->id())
                        )
                    ),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('careTeam', 'user_id', auth()->id())
                        )
                    ),
                SelectFilter::make('my_segments')
                    ->label('My Population Segments')
                    ->options(
                        auth()->user()->segments()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->segmentFilter($query, $data)),
                SelectFilter::make('all_segments')
                    ->label('All Population Segments')
                    ->options(
                        Segment::all()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->segmentFilter($query, $data)),
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
                SelectFilter::make('status_id')
                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('order'))
                    ->multiple()
                    ->preload()
                    ->default(! is_null(SystemAlertStatusClassification::default()) ? [SystemAlertStatusClassification::default()] : []),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form([
                    EducatableSelect::make('concern')
                        ->label('Related To')
                        ->required(),
                    Group::make()
                        ->schema([
                            Textarea::make('description')
                                ->required()
                                ->string(),
                            Select::make('severity')
                                ->options(AlertSeverity::class)
                                ->selectablePlaceholder(false)
                                ->default(AlertSeverity::default())
                                ->required()
                                ->enum(AlertSeverity::class),
                            Textarea::make('suggested_intervention')
                                ->required()
                                ->string(),
                            Select::make('status_id')
                                ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('order'))
                                ->selectablePlaceholder(false)
                                ->default(SystemAlertStatusClassification::default())
                                ->required(),
                        ])
                        ->columns(),
                ]),
        ];
    }

    protected function segmentFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $segment = Segment::find($data['value']);

        /** @var Model $model */
        $model = resolve($segment->model->class());

        $query->whereIn(
            'concern_id',
            app(TranslateSegmentFilters::class)
                ->handle($data['value'])
                ->pluck($model->getQualifiedKeyName()),
        );
    }
}
