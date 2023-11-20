<?php

namespace Assist\Engagement\Actions\Contracts;

interface EngagementChannel
{
    public function deliver(): void;
}
