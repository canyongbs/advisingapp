<div
    id="{{ $getId() }}"
    aria-labelledby="{{ $getId() }}"
    role="tabpanel"
    wire:key="{{ $getLivewireKey() }}.container"
    x-bind:class="{ 'fi-active': $wire.activeTab === @js($getKey(false)) }"
    {{ $getExtraAttributeBag()->class(['fi-sc-tabs-tab', 'fi-active' => $getLivewire()->activeTab === $getKey(false)]) }}
>
    {{ $getChildSchema() }}
</div>
