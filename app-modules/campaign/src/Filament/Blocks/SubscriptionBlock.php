<?php

namespace Assist\Campaign\Filament\Blocks;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;

class SubscriptionBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            Select::make($fieldPrefix . 'user_ids')
                ->label('Who should be subscribed?')
                ->options(User::all()->pluck('name', 'id'))
                ->multiple()
                ->searchable()
                ->default([$user->id])
                ->required()
                ->exists('users', 'id'),
            Toggle::make($fieldPrefix . 'remove_prior')
                ->label('Remove all prior subscriptions?')
                ->default(false)
                ->hintIconTooltip('If checked, all prior care subscriptions will be removed.'),
            DateTimePicker::make($fieldPrefix . 'execute_at')
                ->label('When should the journey step be executed?')
                ->required()
                ->minDate(now($user->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'subscription';
    }
}
