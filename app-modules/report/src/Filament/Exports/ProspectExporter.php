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

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Prospect\Models\Prospect;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Columns\TextColumn;

class ProspectExporter extends Exporter
{
    protected static ?string $model = Prospect::class;

    /**
     * @param class-string<TextColumn | ExportColumn> $type
     */
    public static function getColumns(string $type = ExportColumn::class): array
    {
        return [
            $type::make('id')
                ->label('ID'),
            static::notDefault($type::make('status.name')),
            static::notDefault($type::make('source.name')),
            $type::make('first_name'),
            $type::make('last_name'),
            static::notDefault($type::make('full_name')),
            static::notDefault($type::make('preferred')
                ->label('Preferred Name')),
            static::notDefault($type::make('description')),
            static::notDefault($type::make('birthdate')),
            static::notDefault($type::make('hsgrad')
                ->label('High School Grad')),
            $type::make('created_at'),
            static::notDefault($type::make('assigned_to.name')),
            static::notDefault($type::make('created_by.name')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your prospect report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    protected static function notDefault(ExportColumn | TextColumn $column): ExportColumn | TextColumn
    {
        if ($column instanceof ExportColumn) {
            $column->enabledByDefault(false);
        }

        return $column;
    }
}
