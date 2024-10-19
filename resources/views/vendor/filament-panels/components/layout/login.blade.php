<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-layout flex h-screen w-full flex-col lg:flex-row-reverse">
        <div class="fi-main-ctn w-full flex flex-col h-full">
            <div class="flex justify-center items-center w-full border-b border-gray-200 mb-4 flex-shrink-0 p-4">
                <x-filament-panels::logo />
            </div>
            
            <main class="fi-main mx-auto flex-grow flex justify-center items-center w-full px-4 md:px-6 lg:px-8 max-w-screen-lg mb-4">
                {{ $slot }}
            </main>
            <div class="mt-auto mb-4 lg:mb-4 inline-block w-full h-16">
                <x-footer class="footer" />
            </div>
        </div>
    </div>
</x-filament-panels::layout.base>
