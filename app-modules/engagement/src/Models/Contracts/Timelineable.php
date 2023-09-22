<?php

namespace Assist\Engagement\Models\Contracts;

use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface Timelineable
{
    public function icon(): string;

    public function sortableBy(): string;

    public function providesCustomView(): bool;

    public function modalViewAction(): ViewAction;

    // We need a method that assures that we return a collection from any relationship this is called on
    public static function getTimeline(Model $forModel): Collection;
}
