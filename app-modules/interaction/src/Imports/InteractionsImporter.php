<?php

namespace Assist\Interaction\Imports;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use App\Filament\Actions\ImportAction\ImportColumn;
use Assist\Interaction\Models\InteractionInstitution;

class InteractionsImporter extends Importer
{
    /**
     * @inheritDoc
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('interactable')
                ->relationship(
                    resolveUsing: function (mixed $state) {
                        $type = str($state)->before(':');
                        $value = str($state)->after(':');

                        match ($type) {
                            'prospect' => Prospect::query()
                                ->whereKey($type)
                                ->orWhere('email', $value)
                                ->first(),
                            'student' => Student::query()
                                ->whereKey($type)
                                ->orWhere('email', $value)
                                ->first(),
                        };
                    },
                )
                ->requiredMapping()
                ->rules(
                    [
                        'starts_with:prospect:,student:',
                    ]
                )
                ->example(fn (): string => 'student:' . Student::query()->value('email') ?? fake()->safeEmail()),
            ImportColumn::make('type')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionType::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionType::query()->value('name')),
            ImportColumn::make('relation')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionRelation::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionRelation::query()->value('name')),
            ImportColumn::make('campaign')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionCampaign::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionCampaign::query()->value('name')),
            ImportColumn::make('driver')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionDriver::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionDriver::query()->value('name')),
            ImportColumn::make('status')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionStatus::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionStatus::query()->value('name')),
            ImportColumn::make('outcome')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionOutcome::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionOutcome::query()->value('name')),
            ImportColumn::make('institution')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionInstitution::query()
                        ->whereKey($state)
                        ->orWhereRaw('lower(name) = ?', [strtolower($state)])
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionInstitution::query()->value('name')),
            ImportColumn::make('start_datetime')
                ->rules(['date_format:Y-m-d H:i:s'])
                ->example('2023-09-28 16:52:50'),
            ImportColumn::make('end_datetime')
                ->rules(['date_format:Y-m-d H:i:s'])
                ->example('2023-09-28 17:00:00'),
            ImportColumn::make('subject')
                ->example('Subject of the interaction.'),
            ImportColumn::make('description')
                ->example('A description of the interaction.'),
        ];
    }

    // TODO: Determine how to use this to prevent the duplicate of records
    //public function resolveRecord(): ?Model
    //{
    //    $email = $this->data['email'];
    //    $email2 = $this->data['email_2'] ?? null;
    //
    //    $emails = [
    //        $email,
    //        ...filled($email2) ? [$email2] : [],
    //    ];
    //
    //    $prospect = Prospect::query()
    //        ->whereIn('email', $emails)
    //        ->orWhereIn('email_2', $emails)
    //        ->first();
    //
    //    return $prospect ?? new Prospect([
    //        'email' => $email,
    //        'email_2' => $email2,
    //    ]);
    //}

    public function beforeCreate(): void
    {
        /** @var Interaction $record */
        $record = $this->record;

        $record['user_id'] = $this->import->user->id;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your interactions import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
