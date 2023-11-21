<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Prospect\Imports;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use App\Filament\Actions\ImportAction\ImportColumn;

class ProspectImporter extends Importer
{
    protected static ?string $model = Prospect::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('first_name')
                ->rules(['required'])
                ->requiredMapping()
                ->example('Jonathan'),
            ImportColumn::make('last_name')
                ->rules(['required'])
                ->requiredMapping()
                ->example('Smith'),
            ImportColumn::make('full_name')
                ->rules(['required'])
                ->requiredMapping()
                ->example('Jonathan Smith'),
            ImportColumn::make('preferred')
                ->example('John'),
            ImportColumn::make('status')
                ->relationship(
                    resolveUsing: fn (mixed $state) => ProspectStatus::query()
                        ->when(
                            Str::isUuid($state),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->where('name', $state),
                        )
                        ->first(),
                )
                ->guess(['status_id', 'status_name'])
                ->requiredMapping()
                ->example(fn (): ?string => ProspectStatus::query()->value('name')),
            ImportColumn::make('source')
                ->relationship(
                    resolveUsing: fn (mixed $state) => ProspectSource::query()
                        ->when(
                            Str::isUuid($state),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->where('name', $state),
                        )
                        ->first(),
                )
                ->guess(['source_id', 'source_name'])
                ->requiredMapping()
                ->example(fn (): ?string => ProspectSource::query()->value('name')),
            ImportColumn::make('description')
                ->example('A description of the prospect.'),
            ImportColumn::make('email')
                ->rules(['required', 'email'])
                ->requiredMapping()
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('email_2')
                ->rules(['email'])
                ->example('johnsmith@hotmail.com'),
            ImportColumn::make('mobile')
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('sms_opt_out')
                ->label('SMS opt out')
                ->boolean()
                ->rules(['boolean'])
                ->example('no'),
            ImportColumn::make('email_bounce')
                ->boolean()
                ->rules(['boolean'])
                ->example('yes'),
            ImportColumn::make('phone')
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('address')
                ->example('123 Main St.'),
            ImportColumn::make('address_2')
                ->example('Apt. 1'),
            ImportColumn::make('birthdate')
                ->rules(['date'])
                ->example('1990-01-01'),
            ImportColumn::make('hsgrad')
                ->example('2009'),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $email = $this->data['email'];
        $email2 = $this->data['email_2'] ?? null;

        $emails = [
            $email,
            ...filled($email2) ? [$email2] : [],
        ];

        $prospect = Prospect::query()
            ->whereIn('email', $emails)
            ->orWhereIn('email_2', $emails)
            ->first();

        return $prospect ?? new Prospect([
            'email' => $email,
            'email_2' => $email2,
        ]);
    }

    public function beforeCreate(): void
    {
        $this->record->assignedTo()->associate($this->import->user);
        $this->record->createdBy()->associate($this->import->user);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prospect import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
