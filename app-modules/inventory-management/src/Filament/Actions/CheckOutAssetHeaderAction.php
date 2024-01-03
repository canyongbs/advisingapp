<?php

namespace AdvisingApp\InventoryManagement\Filament\Actions;

use Filament\Forms\Get;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\AssetStatus;

class CheckOutAssetHeaderAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->label(__('Check Out'));

        /** @var Asset $asset */
        $asset = $this->getRecord();

        $this->modalHeading(__("Check out {$asset->name}"));

        $this->modalSubmitActionLabel(__('Done'));

        $this->successNotificationTitle(__("Successfully checked out {$asset->name}"));

        $this->form([
            Radio::make('checked_out_to_type')
                ->label('Check out to')
                ->options([
                    Student::class => 'Student',
                    Prospect::class => 'Prospect',
                ])
                ->default(Student::class)
                ->required()
                ->live(),
            Select::make('checked_out_to_id')
                ->label(fn (Get $get): string => match ($get('checked_out_to_type')) {
                    Student::class => 'Select Student',
                    Prospect::class => 'Select Prospect',
                })
                ->visible(fn (Get $get): bool => filled($get('checked_out_to_type')))
                ->getSearchResultsUsing(function (string $search, Get $get) {
                    return match ($get('checked_out_to_type')) {
                        Student::class => Student::where('full_name', 'like', "%{$search}%")->pluck('full_name', 'sisid')->toArray(),
                        Prospect::class => Prospect::where('full_name', 'like', "%{$search}%")->pluck('full_name', 'id')->toArray(),
                    };
                })
                ->searchable()
                ->required(),
            Textarea::make('notes')
                ->autofocus()
                ->required(),
            DateTimePicker::make('expected_check_in_at')
                ->label('Expected Return Date'),
        ]);

        $this->action(function (array $data): void {
            /** @var Asset $asset */
            $asset = $this->getRecord();

            if (! $asset->isAvailable()) {
                $this->failure();
            }

            $asset->checkOuts()->create([
                'checked_out_by_type' => auth()->user()?->getMorphClass(),
                'checked_out_by_id' => auth()->user()?->id,
                'checked_out_to_type' => (new $data['checked_out_to_type']())->getMorphClass(),
                'checked_out_to_id' => $data['checked_out_to_id'],
                'notes' => $data['notes'],
                'checked_out_at' => now(),
                'expected_check_in_at' => $data['expected_check_in_at'],
            ]);

            // TODO We may want to move this to an observer in order to clean up...
            $checkedOutStatus = AssetStatus::checkedOut()->first();

            $asset->update([
                'status_id' => $checkedOutStatus->id,
            ]);

            $this->success();
        });
    }
}
