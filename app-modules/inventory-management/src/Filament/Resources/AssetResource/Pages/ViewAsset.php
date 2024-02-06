<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\InventoryManagement\Models\Asset;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;
use AdvisingApp\InventoryManagement\Filament\Actions\CheckInAssetHeaderAction;
use AdvisingApp\InventoryManagement\Filament\Actions\CheckOutAssetHeaderAction;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('serial_number'),
                        TextEntry::make('name'),
                        TextEntry::make('description'),
                        TextEntry::make('type.name')
                            ->label('Type'),
                        TextEntry::make('location.name')
                            ->label('Location'),
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('purchase_age')
                            ->label('Device Age')
                            ->helperText(fn (Asset $record) => $record->purchase_date->format('M j, Y')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            CheckOutAssetHeaderAction::make('check-out')
                ->disabled(function () {
                    /** @var Asset $asset */
                    $asset = $this->getRecord();

                    return ! $asset->isAvailable();
                }),
            CheckInAssetHeaderAction::make('check-in')
                ->visible(function () {
                    /** @var Asset $asset */
                    $asset = $this->getRecord();

                    return $asset->isCheckedOut();
                }),
        ];
    }
}
