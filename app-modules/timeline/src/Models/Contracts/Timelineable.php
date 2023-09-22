<?php

namespace Assist\Timeline\Models\Contracts;

use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface Timelineable
{
    public function icon(): string;

    public function sortableBy(): string;

    public function providesCustomView(): bool;

    public function modalViewAction(): ViewAction;

    public static function getTimeline(Model $forModel): Collection;
}
