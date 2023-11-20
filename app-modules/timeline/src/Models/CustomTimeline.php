<?php

namespace Assist\Timeline\Models;

use Assist\Timeline\Models\Contracts\Timelineable;
use Assist\Timeline\Models\Contracts\RendersCustomTimelineView;

abstract class CustomTimeline implements Timelineable, RendersCustomTimelineView {}
