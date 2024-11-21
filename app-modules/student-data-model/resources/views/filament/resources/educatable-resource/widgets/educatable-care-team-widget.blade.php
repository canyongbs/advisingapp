<x-filament-widgets::widget>
    @php
        $careTeam = $this->getCareTeam();
    @endphp

    <x-filament::section @class([
        'fi-section-has-subsections h-full',
        'fi-scrollable' => $careTeam,
    ])>
        <x-slot name="heading">
            Care Team
        </x-slot>

        <x-slot name="headerActions">
            <x-filament::button
                x-on:click="$dispatch('open-modal', { id: 'feed' })"
                color="gray"
            >
                Manage
            </x-filament::button>
        </x-slot>

        @forelse ($careTeam as $careTeamUser)
            <div class="flex w-full items-center gap-6 px-6 py-3">
                <x-filament::avatar
                    class="shrink-0"
                    :href="filament()->getUserAvatarUrl($careTeamUser)"
                    size="lg"
                />

                <div class="grid flex-1 gap-y-0.5">
                    <p class="font-medium text-gray-950 dark:text-white">
                        {{ $careTeamUser->name }}
                    </p>

                    @if (filled($careTeamUser->job_title))
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $careTeamUser->job_title }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6">
                <div class="mx-auto grid max-w-lg justify-items-center gap-4 text-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        @svg('heroicon-o-user-group', 'h-6 w-6 text-gray-500 dark:text-gray-400')
                    </div>

                    <h4 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No care team
                    </h4>
                </div>
            </div>
        @endforelse
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
