<x-filament-panels::layout.base :livewire="$livewire">
    <div class="flex min-h-screen w-full flex-col bg-trout-50 dark:bg-gray-950">
        <div
            class="flex w-full flex-shrink-0 items-center justify-center border-b border-gray-200 p-4 dark:border-white/10"
        >
            <x-filament-panels::logo />
        </div>

        <div class="mx-auto flex w-full max-w-screen-lg flex-1 items-center justify-center px-4 py-8 md:px-6 lg:px-8">
            {{ $slot }}
        </div>

        <div class="mx-auto w-full max-w-screen-lg px-4 md:px-6 lg:px-8">
            <div
                class="flex w-full items-center justify-center border-t border-gray-200 pt-6 pb-4 dark:border-white/10"
            >
                <x-footer class="footer" />
            </div>
        </div>
    </div>
</x-filament-panels::layout.base>
