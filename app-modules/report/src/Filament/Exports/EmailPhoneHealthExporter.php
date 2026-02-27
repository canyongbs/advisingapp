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

use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmailPhoneHealthExporter extends Exporter
{
    public const EXPORT_NAME = 'Student Email and Phone Health Export';

    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('full_name')
                ->label('Name'),
            ExportColumn::make('email_status')
                ->state(fn (Student $record) => $record->canReceiveEmail() ? 'Healthy' : 'Unhealthy')
                ->label('Email Status'),
            ExportColumn::make('is_primary_email_set')
                ->state(fn (Student $record) => filled($record->primaryEmailAddress) ? 'Yes' : 'No')
                ->label('Institutional Email Set'),
            ExportColumn::make('email_bounced')
                ->state(fn (Student $record) => $record->primaryEmailAddress && ($record->primaryEmailAddress->bounced !== null) ? 'Yes' : 'No')
                ->label('Email Bounce'),
            ExportColumn::make('is_email_opted_out')
                ->state(fn (Student $record) => $record->primaryEmailAddress && ($record->primaryEmailAddress->optedOut?->status === EmailAddressOptInOptOutStatus::OptedOut) ? 'Yes' : 'No')
                ->label('Email Opt Out'),
            ExportColumn::make('phone_status')
                ->state(fn (Student $record) => $record->canReceiveSms() ? 'Healthy' : 'Unhealthy')
                ->label('Phone Status'),
            ExportColumn::make('is_primary_phone_set')
                ->state(fn (Student $record) => filled($record->primaryPhoneNumber) ? 'Yes' : 'No')
                ->label('Primary Phone Set'),
            ExportColumn::make('can_receive_sms')
                ->state(fn (Student $record) => (filled($record->primaryPhoneNumber?->number) && $record->primaryPhoneNumber->can_receive_sms) ? 'Yes' : 'No')
                ->label('SMS Capable'),
            ExportColumn::make('is_sms_opted_out')
                ->state(fn (Student $record) => $record->primaryPhoneNumber && $record->primaryPhoneNumber->smsOptOut()->exists() ? 'Yes' : 'No')
                ->label('SMS Opt Out'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your ' . self::EXPORT_NAME . ' export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
