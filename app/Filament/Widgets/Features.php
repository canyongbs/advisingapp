<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Features extends Widget
{
    protected static string $view = 'filament.widgets.features';

    protected int | string | array $columnSpan = 'full';
}
