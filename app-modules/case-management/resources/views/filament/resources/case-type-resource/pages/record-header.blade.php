<header class="flex flex-col gap-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex flex-col">
            <x-filament::breadcrumbs
                class="mb-2 hidden sm:block"
                :breadcrumbs="$breadcrumbs"
            />

            <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $heading }}
            </h1>
        </div>

        <div class="flex shrink-0 items-center gap-3 sm:mt-7">
            <x-filament::actions :actions="$actions" />
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
