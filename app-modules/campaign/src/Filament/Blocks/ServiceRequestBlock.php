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

use Closure;
use App\Models\User;
use Assist\Division\Models\Division;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DateTimePicker;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

class ServiceRequestBlock extends CampaignActionBlock
{
    protected Model | string | Closure | null $model = ServiceRequest::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Service Request');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'division_id')
                ->relationship('division', 'name')
                ->label('Division')
                ->required()
                ->exists((new Division())->getTable(), 'id'),
            Select::make($fieldPrefix . 'status_id')
                ->relationship('status', 'name')
                ->preload()
                ->label('Status')
                ->required()
                ->exists((new ServiceRequestStatus())->getTable(), 'id'),
            Select::make($fieldPrefix . 'priority_id')
                ->relationship(
                    name: 'priority',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                )
                ->label('Priority')
                ->required()
                ->exists((new ServiceRequestPriority())->getTable(), 'id'),
            Select::make($fieldPrefix . 'type_id')
                ->relationship('type', 'name')
                ->preload()
                ->label('Type')
                ->required()
                ->exists((new ServiceRequestType())->getTable(), 'id'),
            Select::make($fieldPrefix . 'assigned_to_id')
                ->relationship('assignedTo', 'name')
                ->searchable()
                ->label('Assign Service Request to')
                ->nullable()
                ->exists((new User())->getTable(), 'id'),
            Textarea::make($fieldPrefix . 'close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make($fieldPrefix . 'res_details')
                ->label('Internal Service Request Details')
                ->nullable()
                ->string(),
            DateTimePicker::make('execute_at')
                ->label('When should the journey step be executed?')
                ->required()
                ->minDate(now(auth()->user()->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'service_request';
    }
}
