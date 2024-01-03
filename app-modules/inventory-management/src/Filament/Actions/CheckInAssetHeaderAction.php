<?php

namespace AdvisingApp\InventoryManagement\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\AssetStatus;

class CheckInAssetHeaderAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->label(__('Check In'));

        /** @var Asset $asset */
        $asset = $this->getRecord();

        $this->modalHeading(__("Check in {$asset->name}"));

        $this->modalSubmitActionLabel(__('Done'));

        $this->successNotificationTitle(__("Successfully checked in {$asset->name}"));

        $this->form([
            Textarea::make('notes')
                ->autofocus(),
            Select::make('status_id')
                ->relationship('status', 'name')
                ->preload()
                ->label('Status')
                ->default(AssetStatus::available()->first()->id)
                ->required()
                ->exists((new AssetStatus())->getTable(), 'id'),
            DateTimePicker::make('checked_in_at')
                ->label('Checked in at')
                ->default(now()),
        ]);

        $this->action(function (array $data): void {
            /** @var Asset $asset */
            $asset = $this->getRecord();

            if (! $asset->isAvailable()) {
                $this->failure();
            }

            $asset->checkIns()->create([
                'checked_in_by_type' => auth()->user()?->getMorphClass(),
                'checked_in_by_id' => auth()->user()?->id,
                // TODO Should we still have this be configurable? Or should the expectation be that this information is the same as the latest check out?
                'checked_in_from_type' => $asset->latestCheckOut->checked_out_to_type,
                'checked_in_from_id' => $asset->latestCheckOut->checked_out_to_id,
                'notes' => $data['notes'],
                'checked_in_at' => $data['checked_in_at'] ?? now(),
            ]);

            $asset->update([
                'status_id' => $data['status_id'],
            ]);

            $this->success();
        });
    }
}
