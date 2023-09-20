<?php

namespace Assist\Prospect\Imports;

use Closure;
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
            ImportColumn::make('status_id')
                ->label('Status')
                ->rules([function (string $attribute, mixed $value, Closure $fail) {
                    $status = ProspectStatus::query()
                        ->when(
                            Str::isUuid($value),
                            fn (Builder $query) => $query->whereKey($value),
                            fn (Builder $query) => $query->where('name', $value),
                        )
                        ->first();

                    if ($status) {
                        return;
                    }

                    $fail('The selected status is invalid.');
                }])
                ->guess(['status'])
                ->fillRecordUsing(function (Prospect $record, string $state) {
                    $status = ProspectStatus::query()
                        ->when(
                            Str::isUuid($state),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->where('name', $state),
                        )
                        ->first();

                    if (! $status) {
                        return;
                    }

                    $record->status()->associate($status);
                })
                ->requiredMapping()
                ->example(ProspectStatus::query()->value('name')),
            ImportColumn::make('source_id')
                ->label('Source')
                ->rules([function (string $attribute, mixed $value, Closure $fail) {
                    $source = ProspectSource::query()
                        ->when(
                            Str::isUuid($value),
                            fn (Builder $query) => $query->whereKey($value),
                            fn (Builder $query) => $query->where('name', $value),
                        )
                        ->first();

                    if ($source) {
                        return;
                    }

                    $fail('The selected source is invalid.');
                }])
                ->guess(['source'])
                ->fillRecordUsing(function (Prospect $record, string $state) {
                    $source = ProspectSource::query()
                        ->when(
                            Str::isUuid($state),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->where('name', $state),
                        )
                        ->first();

                    if (! $source) {
                        return;
                    }

                    $record->source()->associate($source);
                })
                ->requiredMapping()
                ->example(ProspectSource::query()->value('name')),
            ImportColumn::make('description')
                ->example('A description of the prospect.'),
            ImportColumn::make('email')
                ->rules(['email'])
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('email_2')
                ->rules(['email'])
                ->example('johnsmith@hotmail.com'),
            ImportColumn::make('mobile')
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('sms_opt_out')
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
        $email = $this->data['email'] ?? null;
        $email2 = $this->data['email_2'] ?? null;

        $emails = array_filter(
            [$email, $email2],
            fn (mixed $state): bool => filled($state),
        );

        if (empty($emails)) {
            return new Prospect();
        }

        $prospect = Prospect::query()
            ->whereIn('email', $emails)
            ->orWhereIn('email_2', $emails)
            ->first();

        if ($prospect) {
            return $prospect;
        }

        return new Prospect([
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
