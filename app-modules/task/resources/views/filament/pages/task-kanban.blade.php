@php use Assist\Task\Enums\TaskStatus; @endphp
<x-filament-panels::page>
    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div class="flex items-start justify-start px-4 mb-6 space-x-4">
                        @foreach($statuses as $status)
                            @php
                                /** @var TaskStatus $status */
                            @endphp
                            <div class="min-w-kanban">
                                <div class="py-4 text-base font-semibold text-gray-900 dark:text-gray-300">{{ $status->displayName() }}</div>

                                <div id="kanban-list-1" class="mb-4 space-y-4 min-w-kanban">
                                    @foreach($tasks[$status->value] as $task)
                                        <div class="flex flex-col max-w-md p-5 transform bg-white rounded-lg shadow cursor-move dark:bg-gray-800">
                                            <div class="flex items-center justify-between pb-4">
                                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $task->description }}
                                                </div>

                                                <button type="button" data-modal-toggle="kanban-card-modal"
                                                        class="p-2 text-sm text-gray-500 rounded-lg dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                                        <path fill-rule="evenodd"
                                                              d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{--                                    {{ if .attachment }}--}}
                                            {{--                                    <div class="flex items-center justify-center pb-4">--}}
                                            {{--                                        <img class="bg-contain rounded-lg" src="{{ .attachment }}" alt="attachment" />--}}
                                            {{--                                    </div>--}}
                                            {{--                                    {{ end }}--}}

                                            <div class="flex flex-col">
                                                <div class="pb-4 text-sm font-normal text-gray-700 dark:text-gray-400">This
                                                    is the task description
                                                </div>

                                                <div class="flex justify-between">
{{--                                                    <div class="flex items-center justify-start">--}}
{{--                                                        <!-- foreach of "users" -->--}}
{{--                                                        <a href="#" data-tooltip-target="user_taskid_user_id" class="-mr-3">--}}
{{--                                                            <img class="border-2 border-white rounded-full h-7 w-7 dark:border-gray-800"--}}
{{--                                                                 src="" alt=""/>--}}
{{--                                                        </a>--}}
{{--                                                        <div id="user_taskid_userid" role="tooltip"--}}
{{--                                                             class="absolute z-50 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">--}}
{{--                                                            User Name--}}
{{--                                                            <div class="tooltip-arrow" data-popper-arrow></div>--}}
{{--                                                        </div>--}}
{{--                                                        <!-- end of "users" foreach -->--}}
{{--                                                    </div>--}}
                                                    <!-- TODO: if task is done -->
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
                                                    @else
                                                        <div class="flex items-center justify-center px-3 text-sm font-medium text-purple-800 bg-purple-100 rounded-lg dark:bg-purple-200">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                                      clip-rule="evenodd"></path>
                                                            </svg>
                                                            3 days left
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
</x-filament-panels::page>
