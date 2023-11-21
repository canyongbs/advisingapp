<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Campaign\Models\Campaign;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\Campaign\Filament\Resources\CampaignResource;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        // TODO Make link to caseload
                        TextEntry::make('caseload.name')
                            ->label('Caseload'),
                        IconEntry::make('enabled')
                            ->boolean(),
                        IconEntry::make('execution_status')
                            ->label('Has Been Executed?')
                            ->getStateUsing(fn (Campaign $record) => $record->hasBeenExecuted())
                            ->boolean(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
        ];
    }
}
