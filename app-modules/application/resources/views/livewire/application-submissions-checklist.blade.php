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
<div class="space-y-4">
    <div class="flex items-start gap-2">
        <div class="flex w-full flex-col gap-1">
            {{ $this->form }}
        </div>

        <x-filament::button class="mt-2 h-[36px]" type="button" wire:click.prevent="addChecklistItem">
            Add
        </x-filament::button>
    </div>

    <div class="mt-4 space-y-2">
        @forelse ($checklistItems as $item)
            <div
                class="group flex items-center justify-between rounded-md border border-transparent p-2 hover:border-gray-300 hover:bg-gray-100 hover:shadow-sm dark:hover:border-gray-600 dark:hover:bg-gray-700"
                wire:key="item-{{ $item->getKey() }}"
            >
                <div class="flex items-center space-x-2">
                    <x-filament::input.checkbox
                        class="text-primary-600 focus:ring-primary-500 h-5 w-5 rounded border-gray-300 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-800"
                        :checked="$item->is_checked"
                        wire:click.stop="toggleItem('{{ $item->getKey() }}')"
                    />

                    <span class="{{ $item->is_checked ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                        {{ $item->title }}
                    </span>
                </div>

                <x-filament::icon-button
                    icon="heroicon-m-trash"
                    color="danger"
                    size="sm"
                    wire:click.stop="deleteItem('{{ $item->getKey() }}')"
                />
            </div>
        @empty
            <p class="text-sm italic text-gray-500 dark:text-gray-400">No checklist items yet.</p>
        @endforelse
    </div>
</div>
