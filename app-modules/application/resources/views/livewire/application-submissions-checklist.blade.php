<div class="space-y-4">
    <div class="flex items-start gap-2">
        <div class="flex w-full flex-col gap-1">
            {{ $this->form }}
        </div>

        <x-filament::button
            class="mt-2 h-[36px]"
            type="button"
            wire:click.prevent="addChecklistItem"
        >
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
                        class="h-5 w-5 rounded border-gray-300 bg-white text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800"
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
