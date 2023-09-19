<?php

namespace Assist\Prospect\Imports;

use Closure;
use App\Imports\Importer;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
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
                ->requiredMapping(),
            ImportColumn::make('last_name')
                ->rules(['required'])
                ->requiredMapping(),
            ImportColumn::make('full_name')
                ->rules(['required'])
                ->requiredMapping(),
            ImportColumn::make('preferred'),
            ImportColumn::make('status_id')
                ->label('Status')
                ->rules([function (string $attribute, mixed $value, Closure $fail) {
                    $status = ProspectStatus::query()
                        ->whereKey($value)
                        ->orWhere('name', $value)
                        ->first();

                    if ($status) {
                        return;
                    }

                    $fail('The selected status is invalid.');
                }])
                ->guess(['status'])
                ->fillRecordUsing(function (Prospect $record, string $state) {
                    $status = ProspectStatus::query()
                        ->where('name', $state)
                        ->first();

                    if (! $status) {
                        return;
                    }

                    $record->status()->associate($status);
                })
                ->requiredMapping(),
            ImportColumn::make('source_id')
                ->label('Source')
                ->rules([function (string $attribute, mixed $value, Closure $fail) {
                    $source = ProspectSource::query()
                        ->whereKey($value)
                        ->orWhere('name', $value)
                        ->first();

                    if ($source) {
                        return;
                    }

                    $fail('The selected source is invalid.');
                }])
                ->guess(['source'])
                ->fillRecordUsing(function (Prospect $record, string $state) {
                    $source = ProspectSource::query()
                        ->where('name', $state)
                        ->first();

                    if (! $source) {
                        return;
                    }

                    $record->source()->associate($source);
                })
                ->requiredMapping(),
            ImportColumn::make('description'),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('email_2')
                ->rules(['email']),
            ImportColumn::make('mobile')
                ->ignoreBlankState(),
            ImportColumn::make('sms_opt_out')
                ->boolean(),
            ImportColumn::make('email_bounce')
                ->boolean(),
            ImportColumn::make('phone'),
            ImportColumn::make('address'),
            ImportColumn::make('address_2'),
            ImportColumn::make('birthdate'),
            ImportColumn::make('hsgrad'),
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

    public static function getCompletedNotificationBody(int $totalRows): string
    {
        return "Your prospect import has completed and {$totalRows} rows were processed.";
    }

    public static function getFailureNotificationBody(int $processedRows): string
    {
        return "Something went wrong after importing {$processedRows} rows of prospects.";
    }

    public function getValidationFailureNotificationBody(string $message): string
    {
        return "A validation error occurred while importing a prospect: {$message}. This skipped row belonged to the prospect with full name \"{$this->data['full_name']}\".";
    }
}
