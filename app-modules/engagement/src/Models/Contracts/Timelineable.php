<?php

namespace Assist\Engagement\Models\Contracts;

interface Timelineable
{
    public function icon(): string;

    public function sortableBy(): string;

    public function providesCustomView(): bool;
}
