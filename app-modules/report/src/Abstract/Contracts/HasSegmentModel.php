<?php

namespace AdvisingApp\Report\Abstract\Contracts;

use AdvisingApp\Segment\Enums\SegmentModel;

interface HasSegmentModel
{
    public function segmentModel(): ?SegmentModel;
}
