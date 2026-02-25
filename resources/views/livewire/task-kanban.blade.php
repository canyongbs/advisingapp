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
    use AdvisingApp\Task\Enums\TaskStatus;
@endphp

<div>
    <div class="mt-2 flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="mb-6 flex items-start justify-start space-x-4 px-4" x-data="kanban($wire)">
                        @foreach ($statuses as $status)
                            @php
                                /** @var TaskStatus $status */
                            @endphp

                            <div class="min-w-kanban">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="py-4 text-base font-semibold text-gray-900 dark:text-gray-300">
                                        {{ $status->getLabel() }}
                                    </div>

                                    <x-filament::link
                                        tag="button"
                                        icon="heroicon-m-plus"
                                        :wire:click="'mountAction(\'createTask\', { status:\'' . $status->value . '\' })'"
                                    >
                                        New
                                    </x-filament::link>
                                </div>

                                <div
                                    id="kanban-list-{{ $status->value }}"
                                    data-status="{{ $status->value }}"
                                    @class(['relative flex flex-col gap-4 min-w-kanban mb-4 h-full', 'pb-20' => ! count($tasks[$status->value])])
                                >
                                    @foreach ($tasks[$status->value] as $task)
                                        <div
                                            class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
                                            data-task="{{ $task->id }}"
                                            wire:key="task-{{ $task->id }}"
                                        >
                                            <div class="flex items-center justify-between pb-4">
                                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ str($task->title)->limit(50) }}
                                                </div>
                                                <x-filament::icon-button
                                                    class="fi-primary-color"
                                                    wire:click="viewTask('{{ $task->id }}')"
                                                    icon="heroicon-m-arrow-top-right-on-square"
                                                />
                                            </div>

                                            <div class="flex flex-col">
                                                <!-- TODO: Need to discuss with product as to whether or not Tasks should have a title AND description? -->
                                                <div class="pb-4 text-sm font-normal text-gray-700 dark:text-gray-400">
                                                    {{ str($task->description)->limit(50) }}
                                                </div>

                                                <div class="flex justify-end">
                                                    @if ($task->status === TaskStatus::Completed)
                                                        <div
                                                            class="flex items-center justify-center rounded-lg bg-green-100 px-3 text-sm font-medium text-green-800 dark:bg-green-200"
                                                        >
                                                            <svg
                                                                class="mr-1 h-4 w-4"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd"
                                                                ></path>
                                                            </svg>
                                                            Done
                                                        </div>
                                                    @elseif (! empty($task->due))
                                                        <div
                                                            @class([
                                                                'flex items-center justify-center px-3 text-sm font-medium rounded-lg',
                                                                'text-danger-800 bg-danger-100 dark:bg-danger-200' => $task->due->isPast(),
                                                                'text-warning-800 bg-warning-100 dark:bg-warning-200' => $task->due->isFuture(),
                                                            ])
                                                        >
                                                            <svg
                                                                class="mr-1 h-4 w-4"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                    clip-rule="evenodd"
                                                                ></path>
                                                            </svg>
                                                            {{ now()->diffForHumans($task->due) }} due
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div
                                        class="absolute flex h-20 w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 py-2 font-semibold text-gray-500 hover:border-gray-300 dark:border-gray-800"
                                    >
                                        <div>Drag tasks here</div>

                                        <button
                                            class="hover:underline"
                                            tag="button"
                                            wire:click="mountAction('createTask', { status: '{{ $status->value }}' })"
                                        >
                                            or add a new one
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>
