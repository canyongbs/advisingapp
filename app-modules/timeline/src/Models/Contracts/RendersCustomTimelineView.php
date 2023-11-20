<?php

namespace Assist\Timeline\Models\Contracts;

interface RendersCustomTimelineView
{
    public function renderCustomView(): string;
}
