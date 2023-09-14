<?php

namespace Assist\Notifications\Filament\Actions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\Notifications\Models\Contracts\Subscribable;

class SubscribeBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-s-bell');

        $this->action(function (Collection $records) {
            return $records->each(function (Subscribable $record) {
                resolve(SubscriptionToggle::class)->handle(auth()->user(), $record);
            });
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'toggle_subscription';
    }
}
