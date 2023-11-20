<?php

namespace Assist\Timeline\Models\Contracts;

interface RendersNormalizedTimelineView
{
    public function timelineRecordTitle(): string;

    public function timelineRecordDatetimeIndicator(): string;

    public function timelineRecordDatetime(): string;

    public function timelineRecordDescription(): string;
}
