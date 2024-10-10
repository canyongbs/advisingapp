<?php

namespace App\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Component;

class StudentProfileInformation extends Component
{
    protected string $view = 'infolists.components.student-profile-information';

    public function __construct(protected string | Closure $heading) {}

    public static function make(string | Closure $heading): static
    {
        return app(static::class, ['heading' => $heading]);
    }

    function heading(string | Closure $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): string | Closure
    {
        return  $this->evaluate($this->heading);
    }
}
