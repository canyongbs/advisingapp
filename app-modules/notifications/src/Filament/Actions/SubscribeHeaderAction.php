<?php

namespace Assist\Notifications\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\Notifications\Models\Contracts\Subscribable;
use Filament\Pages\Concerns\InteractsWithHeaderActions;

class SubscribeHeaderAction extends Action
{
    use InteractsWithHeaderActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->label(function (Subscribable $record) {
            return $record
                ->subscriptions()
                ->whereHas('user', function (Builder $query) {
                    return $query->where('user_id', auth()->id());
                })
                ->exists() ? 'Unsubscribe' : 'Subscribe';
        });

        $this->action(function (Subscribable $record) {
            resolve(SubscriptionToggle::class)->handle(auth()->user(), $record);

            $this->dispatch('refreshRelations');

            $this->cachedHeaderActions = [];
            $this->cacheHeaderActions();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'subscribe';
    }
}
