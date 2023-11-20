<?php

namespace Assist\Notifications\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Assist\Notifications\Models\Contracts\Subscribable;
use Illuminate\Database\UniqueConstraintViolationException;
use Assist\Notifications\Exceptions\SubscriptionAlreadyExistsException;

class SubscriptionCreate
{
    public function handle(User $user, Subscribable $subscribable, bool $throwUniqueException = true): void
    {
        try {
            DB::transaction(
                fn () => $user->subscriptions()
                    ->create([
                        'subscribable_id' => $subscribable->getKey(),
                        'subscribable_type' => $subscribable->getMorphClass(),
                    ])
            );
        } catch (UniqueConstraintViolationException $exception) {
            if ($throwUniqueException) {
                throw new SubscriptionAlreadyExistsException(
                    previous: $exception,
                );
            }
        }
    }
}
