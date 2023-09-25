<?php

namespace Assist\CaseloadManagement\Filament\Resources;

use App\Filament\Pages\Concerns\HasNavigationGroup;
use Exception;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use App\Filament\Enums\NavigationGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Filters\TernaryFilter;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\EditCaseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\ListCaseloads;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\CreateCaseload;

class CaseloadResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = Caseload::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function filters(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentFilters(),
            CaseloadModel::Prospect => static::prospectFilters(),
            default => throw new Exception("{$subject->name} filters not implemented"),
        };
    }

    public static function columns(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentColumns(),
            CaseloadModel::Prospect => static::prospectColumns(),
            default => throw new Exception("{$subject->name} columns not implemented"),
        };
    }

    public static function actions(CaseloadModel $subject): array
    {
        return match ($subject) {
            CaseloadModel::Student => static::studentActions(),
            CaseloadModel::Prospect => static::prospectActions(),
            default => throw new Exception("{$subject->name} actions not implemented"),
        };
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseloads::route('/'),
            'create' => CreateCaseload::route('/create'),
            'edit' => EditCaseload::route('/{record}/edit'),
        ];
    }

    private static function studentFilters(): array
    {
        return [
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
        ];
    }

    private static function prospectFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->relationship('status', 'name')
                ->multiple()
                ->preload(),
            SelectFilter::make('source')
                ->relationship('source', 'name')
                ->multiple()
                ->preload(),
        ];
    }

    private static function studentColumns(): array
    {
        return [
            TextColumn::make(Student::displayNameKey())
                ->label('Name')
                ->sortable(),
            TextColumn::make('email'),
            TextColumn::make('mobile'),
            TextColumn::make('phone'),
            TextColumn::make('sisid'),
            TextColumn::make('otherid'),
        ];
    }

    private static function prospectColumns(): array
    {
        return [
            TextColumn::make(Prospect::displayNameKey())
                ->label('Name')
                ->sortable(),
            TextColumn::make('email')
                ->label('Email')
                ->sortable(),
            TextColumn::make('mobile')
                ->label('Mobile')
                ->sortable(),
            TextColumn::make('status')
                ->badge()
                ->state(function (Prospect $record) {
                    return $record->status->name;
                })
                ->color(function (Prospect $record) {
                    return $record->status->color;
                })
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query
                        ->join('prospect_statuses', 'prospects.status_id', '=', 'prospect_statuses.id')
                        ->orderBy('prospect_statuses.name', $direction);
                }),
            TextColumn::make('source.name')
                ->label('Source')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('g:ia - M j, Y ')
                ->sortable(),
        ];
    }

    private static function studentActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
        ];
    }

    private static function prospectActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
        ];
    }
}
