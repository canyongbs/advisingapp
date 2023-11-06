@php use Filament\Facades\Filament; @endphp

<x-layout>
    <section>
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <div class="mx-auto text-center">
                <img class="mx-auto mb-3 max-w-md"
                     src="{{ Vite::asset('resources/svg/flowbite-500.svg') }}"
                     alt="Internal Server Error"
                >
                <h1 class="mb-3 text-5xl font-extrabold text-primary-600 dark:text-primary-500">
                    Something has gone seriously wrong
                </h1>
                <p class="mb-5 tracking-tight font-bold text-gray-900 md:text-xl dark:text-gray-400">
                    It's always time for a coffee break. We should be back by the time you finish your coffee.
                </p>
                <x-filament::button
                    href="{{ Filament::getUrl() }}"
                    icon="heroicon-o-chevron-left"
                    tag="a"
                    class="inline-flex text-white bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-primary-900"
                >
                    Go back home
                </x-filament::button>
            </div>
        </div>
    </section>
</x-layout>
