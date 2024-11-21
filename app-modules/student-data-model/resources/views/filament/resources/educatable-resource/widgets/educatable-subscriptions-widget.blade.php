<x-filament-widgets::widget>
    @php
        $subscribedUsers = $this->getSubscribedUsers();
    @endphp

    <x-filament::section @class([
        'fi-section-has-subsections h-full',
        'fi-scrollable' => $subscribedUsers,
    ])>
        <x-slot name="heading">
            Subscriptions
        </x-slot>

        <x-slot name="headerActions">
            <x-filament::button
                color="gray"
                tag="a"
                :href="$manageUrl"
            >
                Manage
            </x-filament::button>
        </x-slot>

        @forelse ($subscribedUsers as $subscriber)
            <div class="flex w-full items-center gap-6 px-6 py-3">
                <x-filament::avatar
                    class="shrink-0"
                    :src="filament()->getUserAvatarUrl($subscriber)"
                    loading="lazy"
                    size="lg"
                />

                <div class="grid flex-1 gap-y-0.5">
                    <p class="font-medium text-gray-950 dark:text-white">
                        {{ $subscriber->name }}
                    </p>

                    @if (filled($subscriber->job_title))
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Subscribed {{ $subscriber->pivot->created_at->toFormattedDateString() }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6">
                <div class="mx-auto grid max-w-lg justify-items-center gap-4 text-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        @svg('heroicon-o-x-mark', 'h-6 w-6 text-gray-500 dark:text-gray-400')
                    </div>

                    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No subscriptions
                    </h4>
                </div>
            </div>
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>
