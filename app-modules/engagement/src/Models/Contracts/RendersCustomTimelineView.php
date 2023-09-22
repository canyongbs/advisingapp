<?php

namespace Assist\Engagement\Models\Contracts;

interface RendersCustomTimelineView
{
    public function renderCustomView(): string;
}
