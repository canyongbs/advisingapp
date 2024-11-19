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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Filament\Blocks;

use Closure;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Division\Models\Division;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use Illuminate\Contracts\Database\Eloquent\Builder;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use AdvisingApp\CaseManagement\Models\ServiceRequestStatus;
use AdvisingApp\CaseManagement\Models\ServiceRequestPriority;

class CaseBlock extends CampaignActionBlock
{
    protected Model | string | Closure | null $model = ServiceRequest::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Case');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'division_id')
                ->relationship('division', 'name')
                ->model(ServiceRequest::class)
                ->label('Division')
                ->required()
                ->exists((new Division())->getTable(), 'id'),
            Select::make($fieldPrefix . 'status_id')
                ->relationship('status', 'name')
                ->model(ServiceRequest::class)
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
                ->model(ServiceRequest::class)
                ->label('Priority')
                ->required()
                ->exists((new ServiceRequestPriority())->getTable(), 'id'),
            Select::make($fieldPrefix . 'assigned_to_id')
                ->relationship('assignedTo.user', 'name')
                ->model(ServiceRequest::class)
                ->searchable()
                ->label('Assign Case to')
                ->nullable()
                ->exists((new User())->getTable(), 'id'),
            Textarea::make($fieldPrefix . 'close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make($fieldPrefix . 'res_details')
                ->label('Internal Case Details')
                ->nullable()
                ->string(),
            DateTimePicker::make('execute_at')
                ->label('When should the journey step be executed?')
                ->columnSpanFull()
                ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                ->hintIconTooltip('This time is set in ' . app(CampaignSettings::class)->getActionExecutionTimezoneLabel() . '.')
                ->lazy()
                ->helperText(fn ($state): ?string => filled($state) ? $this->generateUserTimezoneHint(CarbonImmutable::parse($state)) : null)
                ->required()
                ->minDate(now()),
        ];
    }

    public static function type(): string
    {
        return 'service_request';
    }
}
