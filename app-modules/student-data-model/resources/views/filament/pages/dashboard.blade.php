@php
    use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
@endphp

<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament::tabs label="Content tabs">
        @foreach (ActionCenterTab::cases() as $tab)
            <x-filament::tabs.item
                wire:click="$set('activeTab', '{{ $tab->value }}')"
                :active="$activeTab === $tab->value"
            >
                {{ $tab->getLabel() }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="[...$this->filters ?? [], ...$this->getWidgetData()]"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
