<?php

namespace Assist\Notifications\Models\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface Subscribable
 *
 * @property string $id
 *
 * @mixin Model
 */
interface Subscribable
{
    public function subscriptions(): MorphMany;
}
