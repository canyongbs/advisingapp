@use('AdvisingApp\Prospect\Filament\Resources\ProspectResource')
<div
    class="z-10 flex max-w-md transform cursor-move flex-col rounded-lg bg-white p-5 shadow dark:bg-gray-800"
    data-pipeline="{{ $pipeline->getKey() }}"
    data-educatable="{{ $educatable->getKey() }}"
    wire:key="pipeline-{{ $pipeline->getKey() }}-{{ time() }}"
>
    <div class="flex items-center justify-between pb-4">
        <div class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $educatable?->full_name }}
            <br>
            <small>
                {{ str($pipeline->name)->limit(50) }}
            </small>
            <br>
            <small>
                {{ str($pipeline?->segment?->name)->limit(50) }}
            </small>
        </div>

        <x-filament::icon-button
            href="{{ ProspectResource::getUrl('view', [
                'record' => $educatable?->getKey(),
            ]) }}"
            icon="heroicon-m-arrow-top-right-on-square"
            tag="a"
            target="_blank"
            label="View Prospect"
        />

    </div>
</div>