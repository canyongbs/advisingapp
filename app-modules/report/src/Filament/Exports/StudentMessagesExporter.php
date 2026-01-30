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

use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Exception;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class StudentMessagesExporter extends Exporter
{
    public const EXPORT_NAME = 'Student Messages';

    protected static ?string $model = HolisticEngagement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('direction')
                ->label('Direction')
                ->formatStateUsing(fn (string $state): string => Str::title($state)),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(function (HolisticEngagement $record): ?string {
                    if (is_null($record->record)) {
                        return null;
                    }

                    return match ($record->record::class) {
                        EngagementResponse::class => $record->record->status->value,
                        Engagement::class => EngagementDisplayStatus::getStatus($record->record)->getLabel(),
                        default => throw new Exception('Invalid record type'),
                    };
                }),
            ExportColumn::make('sent_by')
                ->label('Sent By')
                ->formatStateUsing(function (HolisticEngagement $record): ?string {
                    if (is_null($record->sentBy)) {
                        return 'System';
                    }

                    return match ($record->sentBy::class) {
                        Student::class, Prospect::class => $record->sentBy->{$record->sentBy->displayNameKey()},
                        User::class => $record->sentBy->name,
                        default => throw new Exception('Invalid sender type'),
                    };
                }),
            ExportColumn::make('sent_to')
                ->label('Sent To')
                ->formatStateUsing(function (HolisticEngagement $record): string {
                    if (is_null($record->sentTo)) {
                        return 'N/A';
                    }

                    return match ($record->sentTo::class) {
                        Student::class, Prospect::class => $record->sentTo->{$record->sentTo->displayNameKey()},
                        default => 'N/A',
                    };
                }),
            ExportColumn::make('type')
                ->label('Type')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'email' => 'Email',
                    'sms' => 'SMS',
                    default => throw new Exception('Invalid type'),
                }),
            ExportColumn::make('details')
                ->label('Details')
                ->formatStateUsing(function (HolisticEngagement $record): ?string {
                    if (is_null($record->record)) {
                        return null;
                    }

                    return match ($record->record::class) {
                        Engagement::class => match ($record->record->channel) {
                            NotificationChannel::Email => $record->record->getSubjectMarkdown(),
                            NotificationChannel::Sms => $record->record->getBodyMarkdown(),
                            default => 'N/A',
                        },
                        EngagementResponse::class => match ($record->record->type) {
                            EngagementResponseType::Email => $record->record->subject,
                            EngagementResponseType::Sms => $record->record->getBodyMarkdown(),
                        },
                        default => throw new Exception('Invalid record type'),
                    };
                }),
            ExportColumn::make('record_sortable_date')
                ->label('Date'),
            ExportColumn::make('campaign')
                ->label('Campaign')
                ->formatStateUsing(function (HolisticEngagement $record): string {
                    if (! ($record->record instanceof Engagement)) {
                        return 'N/A';
                    }

                    return $record->record->campaignAction?->campaign->name ?? 'N/A';
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student messages detail export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
