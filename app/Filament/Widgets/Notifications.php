<?php

namespace App\Filament\Widgets;

use Carbon\CarbonInterface;
use Livewire\Attributes\On;
use Filament\Widgets\Widget;
use Livewire\WithPagination;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\Relation;

class Notifications extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.notifications';

    protected int | string | array $columnSpan = 'full';

    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->delete();
    }

    public function clearNotifications(): void
    {
        $this->getNotificationsQuery()->delete();
    }

    public function markAllNotificationsAsRead(): void
    {
        $this->getUnreadNotificationsQuery()->update(['read_at' => now()]);
    }

    public function getNotifications(): Paginator
    {
        return $this->getNotificationsQuery()->simplePaginate(10);
    }

    public function getNotificationsQuery(): Builder | Relation
    {
        return auth()->user()->notifications()->where('data->format', 'filament');
    }

    public function getUnreadNotificationsQuery(): Builder | Relation
    {
        return $this->getNotificationsQuery()->unread();
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->getUnreadNotificationsQuery()->count();
    }

    public function getBroadcastChannel(): ?string
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        if (method_exists($user, 'receivesBroadcastNotificationsOn')) {
            return $user->receivesBroadcastNotificationsOn();
        }

        $userClass = str_replace('\\', '.', $user::class);

        return "{$userClass}.{$user->getKey()}";
    }

    public function getNotification(DatabaseNotification $notification): Notification
    {
        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    /**
     * @return array<string>
     */
    public function queryStringHandlesPagination(): array
    {
        return [];
    }

    protected function formatNotificationDate(CarbonInterface $date): string
    {
        return $date->diffForHumans();
    }
}
