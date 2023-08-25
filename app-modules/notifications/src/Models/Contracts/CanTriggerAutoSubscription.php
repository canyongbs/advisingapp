<?php

namespace Assist\Notifications\Models\Contracts;

interface CanTriggerAutoSubscription
{
    public function getSubscribable(): ?Subscribable;
}
