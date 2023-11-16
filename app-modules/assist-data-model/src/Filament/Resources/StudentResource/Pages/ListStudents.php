<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Filters\TernaryFilter;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\CareTeam\Filament\Actions\ToggleCareTeamBulkAction;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;
use App\Filament\Columns\OpenSearch\TextColumn as OpenSearchTextColumn;

class ListStudents extends ListRecords
{
    use FilterTableWithOpenSearch;

    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                OpenSearchTextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                OpenSearchTextColumn::make('email')
                    ->searchable(),
                OpenSearchTextColumn::make('mobile')
                    ->searchable(),
                OpenSearchTextColumn::make('phone')
                    ->searchable(),
                OpenSearchTextColumn::make('sisid')
                    ->searchable(),
                OpenSearchTextColumn::make('otherid')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('my_caseloads')
                    ->label('My Caseloads')
                    ->options(
                        auth()->user()->caseloads()
                            ->where('model', CaseloadModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                SelectFilter::make('all_caseloads')
                    ->label('All Caseloads')
                    ->options(
                        Caseload::all()
                            ->where('model', CaseloadModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
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
                ViewAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'students'),
                    DeleteBulkAction::make(),
                    ToggleCareTeamBulkAction::make(),
                ]),
            ]);
    }

    protected function caseloadFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $query->whereKey(
            app(TranslateCaseloadFilters::class)
                ->handle($data['value'])
                ->pluck($query->getModel()->getQualifiedKeyName()),
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
