@php
    use Illuminate\Support\Str;
    use AdvisingApp\Ai\Enums\AiPromptTabs;
@endphp

<div class="mx-auto w-full max-w-7xl px-4 py-6">
    <div class="mb-6">
        <h2 class="text-center text-xl font-semibold text-gray-800 dark:text-white">
            Consider getting started with one of our Smart Prompts!
        </h2>
    </div>

    <div class="mb-6 flex justify-center">
        <x-filament::tabs label="Prompt tabs">
            @foreach (AiPromptTabs::cases() as $tab)
                <x-filament::tabs.item
                    wire:click="$set('activeTab', '{{ $tab->value }}')"
                    :active="$activeTab === $tab->value"
                >
                    {{ $tab->getLabel() }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($prompts as $prompt)
            <x-filament::card
                class="overflow-hidden rounded-xl border bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-800"
            >
                <div class="flex h-20 flex-col justify-start">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $prompt->type->title }}
                    </h3>
                    <p class="mt-2 line-clamp-4 text-sm text-gray-600 dark:text-gray-300">
                        {{ Str::limit($prompt->title, 50) }}
                    </p>
                </div>
            </x-filament::card>
        @empty
            <p class="col-span-full text-center text-sm text-gray-500 dark:text-gray-400">
                @switch($activeTab)
                    @case('newest')
                        No new prompts available.
                    @break

                    @case('most')
                        No prompts have been liked yet.
                    @break

                    @case('most_viewed')
                        No prompts have been viewed yet.
                    @break

                    @default
                        No prompts available.
                @endswitch
            </p>
        @endforelse
    </div>

    <div class="mt-6 flex justify-end">
        <x-filament::link
            tag="button"
            size="sm"
            color="primary"
            wire:click="mountAction('insertFromPromptLibrary')"
        >
            View full prompt library
        </x-filament::link>
    </div>

    <x-filament-actions::modals />
</div>
