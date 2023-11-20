<?php

namespace Assist\Interaction\Imports;

use App\Models\Import;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use App\Filament\Actions\ImportAction\ImportColumn;

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

                        return match ($type->toString()) {
                            'prospect' => Prospect::query()
                                ->when(
                                    str($value)->isUuid(),
                                    fn (Builder $query) => $query->whereKey($value),
                                    fn (Builder $query) => $query->where('email', $value),
                                )
                                ->first(),
                            'student' => Student::query()
                                ->when(
                                    str($value)->isUuid(),
                                    fn (Builder $query) => $query->whereKey($value),
                                    fn (Builder $query) => $query->where('email', $value),
                                )
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
                ->example('student:johnsmith@gmail.com'),
            ImportColumn::make('type')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionType::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionType::query()->value('name')),
            ImportColumn::make('relation')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionRelation::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionRelation::query()->value('name')),
            ImportColumn::make('campaign')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionCampaign::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionCampaign::query()->value('name')),
            ImportColumn::make('driver')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionDriver::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionDriver::query()->value('name')),
            ImportColumn::make('status')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionStatus::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionStatus::query()->value('name')),
            ImportColumn::make('outcome')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionOutcome::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionOutcome::query()->value('name')),
            ImportColumn::make('division')
                ->relationship(
                    resolveUsing: fn (mixed $state) => Division::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => Division::query()->value('name')),
            ImportColumn::make('start_datetime')
                ->rules(['date'])
                ->example('2023-09-28 16:52:50'),
            ImportColumn::make('end_datetime')
                ->rules(['date'])
                ->example('2023-09-28 17:00:00'),
            ImportColumn::make('subject')
                ->example('Subject of the interaction.'),
            ImportColumn::make('description')
                ->example('A description of the interaction.'),
        ];
    }

    public function resolveRecord(): Interaction
    {
        return new Interaction();
    }

    public function afterFill(): void
    {
        /** @var Interaction $record */
        $record = $this->record;

        $query = Interaction::query();

        foreach ($record->getAttributes() as $key => $value) {
            if (in_array($key, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $query->where($key, $value);
        }

        $existingRecord = $query->first();

        if ($existingRecord) {
            $this->record = $existingRecord;
        }
    }

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
