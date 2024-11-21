<header class="flex flex-col gap-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex flex-col gap-3">
            <x-filament::breadcrumbs
                class="hidden sm:block"
                :breadcrumbs="$breadcrumbs"
            />

            <div class="flex gap-6">
                <div
                    class="flex h-16 w-16 select-none items-center justify-center overflow-hidden rounded-full bg-blue-500 text-2xl tracking-tighter text-white">
                    {{ $educatableInitials }}
                </div>

                <div class="flex-1">
                    <div class="flex h-16 items-center">
                        <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                            {{ $educatableName }}
                        </h1>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div
                            class="flex flex-wrap items-center gap-3 text-sm font-medium text-gray-600 dark:text-gray-400 lg:gap-6">
                            @foreach ($details as [$detailLabel, $detailIcon])
                                <div class="flex items-center gap-2">
                                    @svg($detailIcon, 'size-5')

                                    {{ $detailLabel }}
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="flex flex-wrap items-center gap-3 text-xs font-semibold text-blue-600 dark:text-blue-400">
                            @foreach ($badges as $badgeLabel)
                                <div class="rounded-lg border border-blue-500 px-3 py-1">
                                    {{ $badgeLabel }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 flex-col gap-3 sm:items-end">
            <x-filament::actions :actions="$actions" />

            @if ($hasSisSystem ?? false)
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Last Updated {{ $educatable->updated_at->setTimezone($timezone)->format('m/d/Y \a\t g:i A') }}
                </p>
            @endif
        </div>
    </div>

    @if ($backButtonUrl)
        <div>
            <x-filament::link
                :href="$backButtonUrl"
                icon="heroicon-m-arrow-left"
            >
                {{ $backButtonLabel }}
            </x-filament::link>
        </div>
    @endif
</header>
