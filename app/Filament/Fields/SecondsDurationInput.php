<?php

namespace App\Filament\Fields;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;

class SecondsDurationInput extends Field
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.fieldset';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns([
            'default' => 4,
        ]);

        $this->schema([
            TextInput::make('days')
                ->default(0)
                ->integer()
                ->minValue(0)
                ->dehydrated(false),
            TextInput::make('hours')
                ->default(0)
                ->integer()
                ->minValue(0)
                ->maxValue(24)
                ->dehydrated(false),
            TextInput::make('minutes')
                ->default(0)
                ->integer()
                ->minValue(0)
                ->maxValue(60)
                ->dehydrated(false),
            TextInput::make('seconds')
                ->default(0)
                ->integer()
                ->minValue(0)
                ->maxValue(60)
                ->dehydrated(false),
        ]);

        $this->formatStateUsing(function (int | array | null $state): array {
            if ($state === null) {
                return [
                    'days' => 0,
                    'hours' => 0,
                    'minutes' => 0,
                    'seconds' => 0,
                ];
            }

            if (is_array($state)) {
                return $state;
            }

            return [
                'days' => $days = floor($state / 86400),
                'hours' => $hours = floor(($state - ($days * 86400)) / 3600),
                'minutes' => $minutes = floor(($state - ($days * 86400) - ($hours * 3600)) / 60),
                'seconds' => floor(($state - ($days * 86400) - ($hours * 3600) - ($minutes * 60))),
            ];
        });

        $this->dehydrateStateUsing(function (array $state): ?int {
            $seconds = ($state['days'] * 86400) +
                ($state['hours'] * 3600) +
                ($state['minutes'] * 60) +
                $state['seconds'];

            if (! $seconds) {
                return null;
            }

            return $seconds;
        });
    }
}
