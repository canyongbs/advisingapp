{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.
    
    Notice:
    
    - You may not provide the software to third parties as a hosted or managed
    service, where the service provides users with access to any substantial set of
    the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
    in the software, and you may not remove or obscure any functionality in the
    software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
    of the licensor in the software. Any use of the licensor’s trademarks is subject
    to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
    same in return. Canyon GBS™ and Advising App™ are registered trademarks of
    Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
@php
    use Illuminate\Support\Str;
    use AdvisingApp\Ai\Enums\AiPromptTabs;

    $normalizeWhitespace = fn ($text) => preg_replace(
        '/[\x{00A0}\x{202F}\x{2007}\x{2009}\x{200A}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{205F}]+/u',
        ' ',
        $text,
    );
@endphp

<div class="mx-auto w-full max-w-7xl px-4 py-6">
    <div class="mb-6">
        <h2 class="text-center text-base font-semibold text-gray-800 dark:text-white">
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
            <div
                class="cursor-pointer"
                wire:click="$dispatch('send-prompt', @js((object) ['prompt' => ['id' => $prompt->getKey(), 'title' => $prompt->title]]))"
            >
                <x-filament::card>
                    <div class="h-18 flex flex-col justify-start">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $normalizeWhitespace($prompt->type->title) }}
                        </h3>
                        <p class="mt-2 line-clamp-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ Str::limit($normalizeWhitespace($prompt->title), 50) }}
                        </p>
                    </div>
                </x-filament::card>
            </div>
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
        <x-filament::link tag="button" size="sm" color="primary" wire:click="mountAction('insertFromPromptLibrary')">
            View full prompt library
        </x-filament::link>
    </div>

    <x-filament-actions::modals />
</div>
