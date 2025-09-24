<?php

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;

class StudentCaseExporter extends Exporter
{
    protected static ?string $model = CaseModel::class;

    /**
     * @param Builder<CaseModel> $query
     *
     * @return Builder<CaseModel>
     */
    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with([
            'respondent',
            'assignedTo.user',
        ]);
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('case_number')
                ->label('Case Number'),
            ExportColumn::make('respondent')
                ->label('Related To')
                ->getStateUsing(function (CaseModel $record): string {
                    $respondent = $record->respondent;
                    assert($respondent instanceof Student);

                    return $respondent->{Student::displayNameKey()};
                }),
            // ExportColumn::make('respondent.sisid')
            //     ->label('SISID'),
            // ExportColumn::make('respondent.otherid')
            //     ->label('Other ID'),
            // ExportColumn::make('assignedTo.user.name')
            //     ->label('Assigned To'),
            ExportColumn::make('response')
                ->label('Response')
                ->getStateUsing(function (CaseModel $record) {
                    return $record->getSlaResponseSeconds();
                }),
            ExportColumn::make('sla_resolution_seconds')
                ->label('Resolution')
                ->getStateUsing(function (CaseModel $record) {
                    return $record->getSlaResolutionSeconds();
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student case export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
