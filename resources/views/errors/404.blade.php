@php use Filament\Facades\Filament; @endphp

<x-layout>
    <section>
        <div class="mx-auto max-w-screen-xl px-4 py-8 lg:px-6 lg:py-16">
            <div class="mx-auto text-center">
                <img
                    class="mx-auto mb-3 max-w-md"
                    src="{{ Vite::asset('resources/svg/flowbite-404.svg') }}"
                    alt="Not Found"
                >
                <h1 class="mb-3 text-5xl font-extrabold text-primary-600 dark:text-primary-500">
                    Page not found
                </h1>
                <p class="mb-5 font-bold tracking-tight text-gray-900 dark:text-gray-400 md:text-xl">
                    Oops! Looks like you followed a bad link. If you think this is a problem with us, please tell us.
                </p>
                <x-filament::button
                    class="inline-flex rounded-lg bg-primary-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900"
                    href="{{ Filament::getUrl() }}"
                    icon="heroicon-o-chevron-left"
                    tag="a"
                >
                    Go back home
                </x-filament::button>
            </div>
        </div>
    </section>
</x-layout>
