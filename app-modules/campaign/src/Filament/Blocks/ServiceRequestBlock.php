<?php

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
            DateTimePicker::make($fieldPrefix . 'execute_at')
                ->label('When should the action be executed?')
                ->required()
                ->minDate(now())
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'service_request';
    }
}
