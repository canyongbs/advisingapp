<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Component;
use Illuminate\Support\Str;

class StudentHeaderSection extends Component
{
    protected string $view = 'infolists.components.student-header-section';

    public string $name = '';

    public static function make(): static
    {
        return app(static::class);
    }

    public function nameWords(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNameWords(): string
    {
        $name = collect(Str::of($this->getState()?->full_name)->explode(' '))
            ->map(function ($word) {
                return Str::substr($word, 0, 1);
            })->implode('');

        return $this->evaluate($name);
    }
}
