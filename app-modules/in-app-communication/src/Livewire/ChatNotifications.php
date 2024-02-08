<?php

namespace AdvisingApp\InAppCommunication\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

#[Lazy]
class ChatNotifications extends Component
{
    public function getNotifications(): Collection
    {
        return auth()->user()->conversations()
            ->wherePivotNull('last_read_at')
            ->orWherePivot('unread_messages_count', '>', 0)
            ->latest('twilio_conversation_user.updated_at')
            ->latest('twilio_conversation_user.created_at')
            ->get();
    }

    public function render(): View
    {
        return view('in-app-communication::livewire.chat-notifications');
    }
}
