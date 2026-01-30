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

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentInteractionUsersExporter extends Exporter
{
    public const EXPORT_NAME = 'Student Interaction Users';

    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('first_interaction_at')
                ->label('First')
                ->getStateUsing(function (User $record) {
                    $first = $record
                        ->interactions()
                        ->whereHasMorph('interactable', Student::class)
                        ->orderBy('created_at')
                        ->first();

                    return $first ? $first->created_at->format('M d, Y') : null;
                }),
            ExportColumn::make('most_recent_interaction_at')
                ->label('Most Recent')
                ->getStateUsing(function (User $record) {
                    $last = $record
                        ->interactions()
                        ->whereHasMorph('interactable', Student::class)
                        ->orderByDesc('created_at')
                        ->first();

                    return $last ? $last->created_at->format('M d, Y') : null;
                }),
            ExportColumn::make('total_interactions')
                ->label('Total')
                ->getStateUsing(function (User $record) {
                    return $record
                        ->interactions()
                        ->whereHasMorph('interactable', Student::class)
                        ->count();
                }),
            ExportColumn::make('total_interactions_percent')
                ->label('Total %')
                ->getStateUsing(function (User $record) {
                    $allInteractions = Interaction::whereHasMorph('interactable', Student::class)->count();
                    $userInteractionsCount = $record
                        ->interactions()
                        ->whereHasMorph('interactable', Student::class)
                        ->count();

                    if ($allInteractions > 0) {
                        $percent = round(($userInteractionsCount / $allInteractions) * 100);

                        return "{$percent}%";
                    }

                    return '0%';
                }),
            ExportColumn::make('avg_interaction_duration')
                ->label('Avg. Duration')
                ->getStateUsing(function (User $record) {
                    $durations = $record
                        ->interactions()
                        ->whereHasMorph('interactable', Student::class)
                        ->get()
                        ->map(function (Interaction $interaction) {
                            return Carbon::parse($interaction->end_datetime)
                                ->diffInMinutes(Carbon::parse($interaction->start_datetime), true);
                        })->filter();

                    if ($durations->count() > 0) {
                        $avg = round($durations->avg());

                        return "{$avg} Min.";
                    }

                    return null;
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your users interaction overview report table export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
