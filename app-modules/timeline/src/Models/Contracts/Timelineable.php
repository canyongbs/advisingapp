<?php

namespace Assist\Timeline\Models\Contracts;

use Filament\Actions\ViewAction;

interface Timelineable
{
    public function icon(): string;

    public function sortableBy(): string;

    public function providesCustomView(): bool;

    public function modalViewAction(): ViewAction;
}
