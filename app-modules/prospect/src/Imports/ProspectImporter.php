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

namespace AdvisingApp\Prospect\Imports;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Models\User;
use App\Settings\ImportSettings;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ProspectImporter extends Importer
{
    protected static ?string $model = Prospect::class;

    public static function getColumns(): array
    {
        $castPhoneNumber = function (ImportColumn $column, ?string $state): ?string {
            if (blank($state)) {
                return null;
            }

            $phoneNumberUtil = PhoneNumberUtil::getInstance();

            // Try to parse the number without a region, which will only work if the phone number is in E164 format already.
            try {
                return $phoneNumberUtil->format(
                    $phoneNumberUtil->parse($state),
                    PhoneNumberFormat::E164,
                );
            } catch (NumberParseException) {
                // Do not use invalid phone numbers.
            }

            $defaultCountry = app(ImportSettings::class)->default_country;

            try {
                return $phoneNumberUtil->format(
                    $phoneNumberUtil->parse($state, $defaultCountry),
                    PhoneNumberFormat::E164,
                );
            } catch (NumberParseException $exception) {
                throw ValidationException::withMessages([
                    $column->getName() => "{$exception->getMessage()} ({$column->getName()})",
                ]);
            }
        };

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
            ImportColumn::make('email_1')
                ->rules(['max:255', 'email'])
                ->example('johnsmith@gmail.com')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('email_1_type')
                ->rules(['max:255'])
                ->example('Personal')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('email_2')
                ->rules(['max:255', 'email'])
                ->example('janesmith@gmail.com')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('email_2_type')
                ->rules(['max:255'])
                ->example('Institutional')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('email_3')
                ->rules(['max:255', 'email'])
                ->example('joesmith@gmail.com')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('email_3_type')
                ->rules(['max:255'])
                ->example('Work')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_1')
                ->rules(['max:255'])
                ->example('+1 (555) 555-5555')
                ->castStateUsing($castPhoneNumber)
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_1_ext')
                ->label('Phone 1 extension')
                ->rules(['integer', 'max_digits:8'])
                ->example('123')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_1_type')
                ->rules(['max:255'])
                ->example('Mobile')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_1_can_receive_sms')
                ->boolean()
                ->rules(['boolean'])
                ->example('true')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_2')
                ->rules(['max:255'])
                ->example('+1 (666) 666-6666')
                ->castStateUsing($castPhoneNumber)
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_2_ext')
                ->label('Phone 2 extension')
                ->rules(['integer', 'max_digits:8'])
                ->example('456')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_2_type')
                ->rules(['max:255'])
                ->example('Home')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_2_can_receive_sms')
                ->boolean()
                ->rules(['boolean'])
                ->example('false')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_3')
                ->rules(['max:255'])
                ->example('+1 (777) 777-7777')
                ->castStateUsing($castPhoneNumber)
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_3_ext')
                ->label('Phone 3 extension')
                ->rules(['integer', 'max_digits:8'])
                ->example('789')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_3_type')
                ->rules(['max:255'])
                ->example('Work')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('phone_3_can_receive_sms')
                ->boolean()
                ->rules(['boolean'])
                ->example('false')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_line_1')
                ->rules(['max:255'])
                ->example('123 Main St.')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_line_2')
                ->rules(['max:255'])
                ->example('Apt. 1')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_line_3')
                ->rules(['max:255'])
                ->example('Suite 1')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_city')
                ->rules(['max:255'])
                ->example('Springfield')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_state')
                ->rules(['max:255'])
                ->example('IL')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_postal')
                ->rules(['max:255'])
                ->example('62701')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_country')
                ->rules(['max:255'])
                ->example('US')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_1_type')
                ->rules(['max:255'])
                ->example('Home')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_line_1')
                ->rules(['max:255'])
                ->example('456 Main St.')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_line_2')
                ->rules(['max:255'])
                ->example('Apt. 2')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_line_3')
                ->rules(['max:255'])
                ->example('Suite 2')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_city')
                ->rules(['max:255'])
                ->example('Springfield')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_state')
                ->rules(['max:255'])
                ->example('IL')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_postal')
                ->rules(['max:255'])
                ->example('62701')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_country')
                ->rules(['max:255'])
                ->example('US')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_2_type')
                ->rules(['max:255'])
                ->example('Work')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_line_1')
                ->rules(['max:255'])
                ->example('789 Main St.')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_line_2')
                ->rules(['max:255'])
                ->example('Apt. 3')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_line_3')
                ->rules(['max:255'])
                ->example('Suite 3')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_city')
                ->rules(['max:255'])
                ->example('Springfield')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_state')
                ->rules(['max:255'])
                ->example('IL')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_postal')
                ->rules(['max:255'])
                ->example('62701')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_country')
                ->rules(['max:255'])
                ->example('US')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('address_3_type')
                ->rules(['max:255'])
                ->example('Other')
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('birthdate')
                ->rules(['date'])
                ->example('1990-01-01'),
            ImportColumn::make('hsgrad')
                ->example('2009'),
        ];
    }

    public function resolveRecord(): Prospect
    {
        return Prospect::query()
            ->whereHas('emailAddresses', fn (Builder $query) => $query->whereIn('address', collect([
                $this->data['email_1'] ?? null,
                $this->data['email_2'] ?? null,
                $this->data['email_3'] ?? null,
            ])->filter(filled(...))->all()))
            ->first() ?? new Prospect();
    }

    public function beforeCreate(): void
    {
        /** @var Prospect $prospect */
        $prospect = $this->record;

        /** @var User $user */
        $user = $this->import->user;

        $prospect->createdBy()->associate($user);
    }

    public function afterCreate(): void
    {
        /** @var Prospect $prospect */
        $prospect = $this->record;

        if (! $prospect->wasRecentlyCreated) {
            $prospect->emailAddresses()->delete();
            $prospect->phoneNumbers()->delete();
            $prospect->addresses()->delete();
        }

        foreach (range(1, 3) as $iteration) {
            if (blank($this->data["email_{$iteration}"] ?? null)) {
                continue;
            }

            $prospect->emailAddresses()->create([
                'address' => $this->data["email_{$iteration}"],
                'type' => $this->data["email_{$iteration}_type"] ?? null,
            ]);
        }

        $prospect->primaryEmailAddress()->associate($prospect->emailAddresses()->first());

        foreach (range(1, 3) as $iteration) {
            if (blank($this->data["phone_{$iteration}"] ?? null)) {
                continue;
            }

            $prospect->phoneNumbers()->create([
                'number' => $this->data["phone_{$iteration}"],
                'ext' => $this->data["phone_{$iteration}_ext"] ?? null,
                'type' => $this->data["phone_{$iteration}_type"] ?? null,
                'can_receive_sms' => $this->data["phone_{$iteration}_can_receive_sms"] ?? false,
            ]);
        }

        $prospect->primaryPhoneNumber()->associate($prospect->phoneNumbers()->first());

        foreach (range(1, 3) as $iteration) {
            if (
                blank($this->data["address_{$iteration}_line_1"] ?? null) &&
                blank($this->data["address_{$iteration}_line_2"] ?? null) &&
                blank($this->data["address_{$iteration}_line_3"] ?? null) &&
                blank($this->data["address_{$iteration}_city"] ?? null) &&
                blank($this->data["address_{$iteration}_state"] ?? null) &&
                blank($this->data["address_{$iteration}_postal"] ?? null) &&
                blank($this->data["address_{$iteration}_country"] ?? null)
            ) {
                continue;
            }

            $prospect->addresses()->create([
                'line_1' => $this->data["address_{$iteration}_line_1"] ?? null,
                'line_2' => $this->data["address_{$iteration}_line_2"] ?? null,
                'line_3' => $this->data["address_{$iteration}_line_3"] ?? null,
                'city' => $this->data["address_{$iteration}_city"] ?? null,
                'state' => $this->data["address_{$iteration}_state"] ?? null,
                'postal' => $this->data["address_{$iteration}_postal"] ?? null,
                'country' => $this->data["address_{$iteration}_country"] ?? null,
                'type' => $this->data["address_{$iteration}_type"] ?? null,
            ]);
        }

        $prospect->primaryAddress()->associate($prospect->addresses()->first());

        $prospect->save();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prospect import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function getJobBatchName(): ?string
    {
        return "prospect-import-{$this->getImport()->getKey()}";
    }
}
