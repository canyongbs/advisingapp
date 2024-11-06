@use('AdvisingApp\Prospect\Filament\Resources\ProspectResource')
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
                                                        {{ str($educatable?->pipeline?->name)->limit(50) }}
                                                    </small>
                                                    <br>
                                                    <small>
                                                        {{ str($educatable?->pipeline?->segment?->name)->limit(50) }}
                                                    </small>
                                                </div>
                                                

                                                <x-filament::icon-button
                                                    icon="heroicon-m-arrow-top-right-on-square"
                                                    href="{{ ProspectResource::getUrl('view',[
                                                    'record' => $educatable?->educatable->getKey() ]) }}"
                                                    tag="a"
                                                    target="_blank"
                                                    label="View Prospect"
                                                />
                                                
                                            </div>
                                        </div>
                                    @endforeach

                                    <div
                                        class="absolute flex h-20 w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 py-2 font-semibold text-gray-500 hover:border-gray-300 dark:border-gray-800">
                                        <div>
                                            Drag pipeline segment here
                                        </div>
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
