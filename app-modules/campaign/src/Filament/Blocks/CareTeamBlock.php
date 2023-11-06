<?php

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
                ->label('When should the action be executed?')
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
