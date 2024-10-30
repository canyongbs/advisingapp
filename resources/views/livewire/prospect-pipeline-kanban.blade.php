<div>
    <div class="mt-2 flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <div
                        class="mb-6 flex items-start justify-start space-x-4 px-4"
                        x-data="kanban($wire)"
                    >
                        @foreach ($stages as $stage)
                            @php
                                /** @var PipelineStage $stage */
                            @endphp
                            <div class="min-w-kanban">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="py-4 text-base font-semibold text-gray-900 dark:text-gray-300">
                                        {{ $stage->name }}
                                    </div>

                                    {{-- <x-filament::link
                                        tag="button"
                                        icon="heroicon-m-plus"
                                        :wire:click="'mountAction(\'createPipeline\', { stage_id:\'' . $stage->getKey() . '\' })'"
                                    >
                                        New
                                    </x-filament::link> --}}
                                </div>

                                <div
                                    id="prospect-kanban-list-{{ $stage->getKey() }}"
                                    data-status="{{ $stage->getKey() }}"
                                    @class([
                                        'relative flex flex-col gap-4 min-w-kanban mb-4 h-full',
                                        'pb-20' => !count($pipelineEducatables[$stage->getKey()]),
                                    ])
                                >
                                    @foreach ($pipelineEducatables[$stage->getKey()] as $educatable)
                                        <div
                                            class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
                                            data-pipeline="{{ $educatable?->pipeline->getKey() }}"
                                            data-educatable="{{ $educatable?->educatable->getKey() }}"
                                            wire:key="pipeline-{{ $educatable?->pipeline->getKey() }}-{{ time() }}"
                                        >
                                            <div class="flex items-center justify-between pb-4">
                                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $educatable?->educatable?->full_name }}
                                                    <br>
                                                    <small>
                                                        {{ __('Pipeline: :name', ['name' => str($educatable?->pipeline?->name)->limit(50)]) }}
                                                    </small>
                                                    <br>
                                                    <small>
                                                        {{ __('Segment: :name', ['name' => str($educatable?->pipeline?->segment?->name)->limit(50)]) }}
                                                    </small>
                                                </div>
                                                {{-- <x-filament::icon-button
                                                    class="fi-primary-color"
                                                    wire:click="viewProspect('{{ $educatable->pipeline_id }}')"
                                                    icon="heroicon-o-eye"
                                                /> --}}
                                            </div>

                                            <div class="flex flex-col">
                                                <!-- TODO: Need to discuss with product as to whether or not Tasks should have a title AND description? -->
                                                <div class="pb-4 text-sm font-normal text-gray-700 dark:text-gray-400">
                                                    {{-- {{ str($task->description)->limit(50) }} --}}
                                                </div>

                                                <div class="flex justify-end">
                                                    <!-- Todo to add stage name -->
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div
                                        class="absolute flex h-20 w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 py-2 font-semibold text-gray-500 hover:border-gray-300 dark:border-gray-800">
                                        <div>
                                            Drag pipeline segment here
                                        </div>

                                        {{-- <button
                                            class="hover:underline"
                                            tag="button"
                                            wire:click="mountAction('createPipeline', { stage_id: '{{ $stage->getKey() }}' })"
                                        >
                                            or add a new one
                                        </button> --}}
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
