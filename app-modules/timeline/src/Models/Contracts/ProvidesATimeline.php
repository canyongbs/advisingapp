<?php

namespace Assist\Timeline\Models\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Assist\Timeline\Models\CustomTimeline;

interface ProvidesATimeline
{
    public function timeline(): CustomTimeline;

    public static function getTimelineData(Model $forModel): Collection;
}
