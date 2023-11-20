<?php

namespace Assist\Notifications\Filament\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\Notifications\Models\Contracts\Subscribable;

class SubscribeTableAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(function (Subscribable $record) {
            return $record
                ->subscriptions()
                ->whereHas('user', function (Builder $query) {
                    return $query->where('user_id', auth()->id());
                })
                ->exists() ? 'Unsubscribe' : 'Subscribe';
        });

        $this->icon(function (Subscribable $record) {
            return $record
                ->subscriptions()
                ->whereHas('user', function (Builder $query) {
                    return $query->where('user_id', auth()->id());
                })
                ->exists() ? 'heroicon-s-bell-slash' : 'heroicon-s-bell';
        });

        $this->action(function (Subscribable $record) {
            resolve(SubscriptionToggle::class)->handle(auth()->user(), $record);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'subscribe';
    }
}
