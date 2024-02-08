@php
    use AdvisingApp\InAppCommunication\Filament\Pages\UserChat;

    $notifications = $this->getNotifications();
@endphp

<div wire:poll.10s>
    <x-filament::modal slide-over>
        <x-slot name="trigger">
            <x-filament::icon-button
                label="Open chat notifications"
                icon="heroicon-o-chat-bubble-oval-left-ellipsis"
                color="gray"
                :badge="count($notifications)"
            />
        </x-slot>

        <x-slot name="heading">
            Chat
        </x-slot>

        @if (count($notifications))
            <div class="chat-notifications -mx-6 border-y border-gray-200 divide-y divide-gray-200 dark:border-white/10 dark:divide-white/10">
                @foreach ($notifications as $notification)
                    <div class="relative before:absolute before:start-0 before:h-full before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500">
                        {{ $notification->participant->getNotification()->inline() }}
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col px-6">
                <div class="mb-5 flex items-center justify-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        <x-heroicon-o-chat-bubble-oval-left-ellipsis class="h-6 w-6 text-gray-500 dark:text-gray-400"/>
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No new messages
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Please check again later.
                    </p>
                </div>
            </div>
        @endif

        <x-slot name="footerActions">
            <x-filament::button :href="UserChat::getUrl()" tag="a">
                Open chat
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
