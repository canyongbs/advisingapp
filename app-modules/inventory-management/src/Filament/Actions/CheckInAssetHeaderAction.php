<?php

namespace AdvisingApp\InventoryManagement\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

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
                ->autofocus()
                ->required(),
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
                'checked_in_at' => now(),
            ]);

            // TODO We may want to move this to an observer in order to clean up...
            $checkedInStatus = AssetStatus::where('classification', SystemAssetStatusClassification::Available)->first();

            $asset->update([
                'status_id' => $checkedInStatus->id,
            ]);

            $this->success();
        });
    }
}
