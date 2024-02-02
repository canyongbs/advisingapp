<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\InventoryManagement\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Models\Scopes\ClassifiedAs;
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
                ->autofocus(),
            Select::make('status_id')
                ->relationship('status', 'name')
                ->preload()
                ->label('Status')
                ->default(AssetStatus::tap(new ClassifiedAs(SystemAssetStatusClassification::Available))->first()->id)
                ->required()
                ->exists((new AssetStatus())->getTable(), 'id'),
            DateTimePicker::make('checked_in_at')
                ->label('Checked in at')
                ->default(now()),
        ]);

        $this->action(function (array $data): void {
            /** @var Asset $asset */
            $asset = $this->getRecord();

            if (! $asset->isCheckedOut()) {
                $this->failure();
            }

            $asset->checkIns()->create([
                'checked_in_by_type' => auth()->user()?->getMorphClass(),
                'checked_in_by_id' => auth()->user()?->id,
                // TODO Should this always simply be the latest check out, or do we want to support
                // The possibility that the person checking in is different than whoever checked out?
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
