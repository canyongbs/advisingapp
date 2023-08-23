<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\Notifications\Actions\SubscriptionToggle;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('subscribe')
                ->button()
                ->label(fn (Student $record) => $record->subscriptions()->whereHas('user', fn (Builder $query) => $query->where('user_id', auth()->id()))->exists() ? 'Unsubscribe' : 'Subscribe')
                ->action(function (Student $record) {
                    resolve(SubscriptionToggle::class)->handle(auth()->user(), $record);

                    $this->dispatch('refreshRelations');

                    $this->cachedHeaderActions = [];
                    $this->cacheHeaderActions();
                }),
            Actions\EditAction::make(),
        ];
    }
}
