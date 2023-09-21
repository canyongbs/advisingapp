@php use Assist\Task\Enums\TaskStatus; @endphp
<x-filament-panels::page>
    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div x-data="kanban($wire)" class="flex items-start justify-start px-4 mb-6 space-x-4">
                        @foreach($statuses as $status)
                            @php
                                /** @var TaskStatus $status */
                            @endphp
                            <div class="min-w-kanban">
                                <div class="py-4 text-base font-semibold text-gray-900 dark:text-gray-300">{{ $status->displayName() }}</div>

                                <div id="kanban-list-{{ $status->value }}" data-status="{{ $status->value }}" class="mb-4 space-y-4 min-w-kanban">
                                    @foreach($tasks[$status->value] as $task)
                                        <div wire:key="task-{{ $task->id }}" data-task="{{ $task->id }}" class="flex flex-col max-w-md p-5 transform bg-white rounded-lg shadow cursor-move dark:bg-gray-800">
                                            <div class="flex items-center justify-between pb-4">
                                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $task->description }}
                                                </div>

                                                <livewire:task-kanban-edit-button :task="$task" />
                                            </div>

                                            <div class="flex flex-col">
                                                <div class="pb-4 text-sm font-normal text-gray-700 dark:text-gray-400">This
                                                    is the task description
                                                </div>

                                                <div class="flex justify-end">
                                                    @if($task->status === TaskStatus::COMPLETED)
                                                        <div class="flex items-center justify-center px-3 text-sm font-medium text-green-800 bg-green-100 rounded-lg dark:bg-green-200">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            Done
                                                        </div>
                                                    @elseif (!empty($task->due))
                                                        <div
                                                            @class([
                                                                'flex items-center justify-center px-3 text-sm font-medium rounded-lg',
                                                                'text-danger-800 bg-danger-100 dark:bg-danger-200' => $task->due->isPast(),
                                                                'text-warning-800 bg-warning-100 dark:bg-warning-200' => $task->due->isFuture(),
                                                            ])
                                                        >
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ now()->diffForHumans($task->due) }} due
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" data-modal-toggle="new-card-modal"
                                        class="flex items-center justify-center w-full py-2 font-semibold text-gray-500 border-2 border-gray-200 border-dashed rounded-lg hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 dark:border-gray-800 dark:hover:border-gray-700 dark:hover:bg-gray-800 dark:hover:text-white">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    Add another card
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kanban', ($wire) => ({
                init() {
                    const kanbanLists = document.querySelectorAll('[id^="kanban-list-"]');

                    kanbanLists.forEach(kanbanList => {
                        window.Sortable.create(kanbanList, {
                            group: 'kanban',
                            animation: 100,
                            forceFallback: true,
                            dragClass: 'drag-card',
                            ghostClass: 'ghost-card',
                            easing: 'cubic-bezier(0, 0.55, 0.45, 1)',
                            onAdd: async function (evt) {
                                try {
                                    const result = await $wire.movedTask(evt.item.dataset.task, evt.from.dataset.status, evt.to.dataset.status);

                                    if (result.original.success) {
                                        new FilamentNotification()
                                            .icon('heroicon-o-check-circle')
                                            .title(result.original.message)
                                            .iconColor('success')
                                            .send()
                                    } else {
                                        new FilamentNotification()
                                            .icon('heroicon-o-x-circle')
                                            .title(result.original.message)
                                            .iconColor('danger')
                                            .send()
                                    }
                                } catch (e) {
                                    new FilamentNotification()
                                        .icon('heroicon-o-x-circle')
                                        .title('Something went wrong, please try again later')
                                        .iconColor('danger')
                                        .send()
                                }
                            },
                        });
                    });
                },
            }))
        })
    </script>
</x-filament-panels::page>
