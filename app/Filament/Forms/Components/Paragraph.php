<?php

namespace App\Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;

class Paragraph extends Component
{
    protected mixed $content = null;

    protected string | Closure | null $defaultView = 'filament.forms.components.paragraph';

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);

        $this->columnSpanFull();
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function content(mixed $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getContent(): mixed
    {
        return $this->evaluate($this->content);
    }
}
