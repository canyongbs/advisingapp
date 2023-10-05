<?php

namespace Assist\Alert\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Assist\Alert\Models\Alert;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Actions\Action;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class AlertCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Alert $alert) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        $concern = $this->alert->concern;

        $name = $concern->{$concern->displayNameKey()};

        $target = resolve(Filament::getModelResource($concern));

        $alertUrl = $target::getUrl('manage-alerts', ['record' => $concern]);

        $alertLink = new HtmlString("<a href='{$alertUrl}' target='_blank' class='underline'>alert</a>");

        $morph = str($concern->getMorphClass());

        $morphUrl = $target::getUrl('view', ['record' => $concern]);

        $morphLink = new HtmlString("<a href='{$morphUrl}' target='_blank' class='underline'>{$name}</a>");

        return FilamentNotification::make()
            ->warning()
            ->title("A {$this->alert->severity->value} severity {$alertLink} has been created for {$morph} {$morphLink}")
            ->actions([
                Action::make('view_alert')
                    ->label('View Alert')
                    ->button()
                    ->url($alertUrl, shouldOpenInNewTab: true),
                Action::make('view_morph')
                    ->label("View {$morph->ucfirst()}")
                    ->button()
                    ->color('gray')
                    ->url($morphUrl, shouldOpenInNewTab: true),
            ])
            ->getDatabaseMessage();
    }
}
