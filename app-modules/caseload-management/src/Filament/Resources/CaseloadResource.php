<?php

namespace Assist\CaseloadManagement\Filament\Resources;

use Exception;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\EditCaseload;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\ListCaseloads;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages\CreateCaseload;

class CaseloadResource extends Resource
{
    protected static ?string $model = Caseload::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mass Engagement';

    protected static ?string $navigationLabel = 'Define Caseload';

    // public static function prospects

    public static function filters(string $class): array
    {
        ray($class);

        return match ($class) {
            'student' => static::studentFilters(),
            'prospect' => static::prospectFilters(),
            default => throw new Exception("{$class} filters not implemented"),
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
            // Filter::make('holds')
            //     ->form([
            //         TextInput::make('hold'),
            //     ])
            //     ->query(function (Builder $query, array $data): Builder {
            //         return $query
            //             ->when(
            //                 $data['hold'],
            //                 fn (Builder $query, $hold): Builder => $query->where('holds', 'ilike', "%{$hold}%"),
            //             );
            //     }),
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
}
