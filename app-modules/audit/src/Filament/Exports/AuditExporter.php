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

namespace AdvisingApp\Audit\Filament\Exports;

use AdvisingApp\Audit\Models\Audit;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class AuditExporter extends Exporter
{
    protected static ?string $model = Audit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('event'),
            ExportColumn::make('auditable_type'),
            ExportColumn::make('auditable_id')
                ->label('Auditable ID'),
            ExportColumn::make('old_values')
                ->listAsJson(),
            ExportColumn::make('new_values')
                ->listAsJson(),
            ExportColumn::make('url'),
            ExportColumn::make('ip_address')
                ->label('IP address'),
            ExportColumn::make('change_agent_id')
                ->label('Change Agent ID')
                ->default('N/A'),
            ExportColumn::make('change_agent_name')
                ->label('Change Agent Name')
                ->state(function ($record) {
                    return $record->change_agent_name ?? 'System';
                }),
            ExportColumn::make('user_agent'),
            ExportColumn::make('tags'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your audit export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    /**
     * Using CSV format causes issues with the JSON commas not being properly escaped by league/csv.
     */
    public function getFormats(): array
    {
        return [ExportFormat::Xlsx];
    }
}
