<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Notifications
        </x-slot>

        @php
            $notifications = $this->getNotifications();
        @endphp

        @if ($notifications->count())
            <div class="-mx-6 -mt-6 divide-y divide-gray-200 dark:divide-white/10 border-b border-gray-200 dark:border-white/10">
                @foreach ($notifications as $notification)
                    <div
                        @class([
                            'relative before:absolute before:start-0 before:h-full before:w-0.5 before:bg-primary-600 dark:before:bg-primary-500' => $notification->unread(),
                        ])
                    >
                        {{ $this->getNotification($notification)->inline() }}
                    </div>
                @endforeach
            </div>

            <x-filament::pagination :paginator="$notifications" class="mt-6" />
        @else
            <div class="flex px-6 flex-col">
                <div class="mb-5 flex items-center justify-center">
                    <div class="rounded-full bg-gray-100 dark:bg-gray-500/20 p-3">
                        <x-heroicon-o-bell-slash class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No notifications
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Please check again later.
                    </p>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
