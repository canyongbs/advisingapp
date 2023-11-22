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

namespace Assist\Campaign\Filament\Blocks;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;

class CareTeamBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Care Team');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'user_ids')
                ->label('Who should be assigned to the care team?')
                ->options(User::all()->pluck('name', 'id'))
                ->multiple()
                ->searchable()
                ->default([auth()->user()->id])
                ->required()
                ->exists('users', 'id'),
            Toggle::make($fieldPrefix . 'remove_prior')
                ->label('Remove all prior care team assignments?')
                ->default(false)
                ->hintIconTooltip('If checked, all prior care team assignments will be removed.'),
            DateTimePicker::make($fieldPrefix . 'execute_at')
                ->label('When should the journey step be executed?')
                ->required()
                ->minDate(now(auth()->user()->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'care_team';
    }
}
