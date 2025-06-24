@php
    use AdvisingApp\Report\Filament\Widgets\RefreshWidget;

    $visibleWidgets = collect($this->getVisibleWidgets())
        ->reject(fn($widget) => $widget->widget === RefreshWidget::class)
        ->all();
@endphp

<x-filament-panels::page class="fi-dashboard-page">
    <div>
        @livewire(RefreshWidget::class, ['cacheTag' => $this->cacheTag])
    </div>

    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="[
            ...property_exists($this, 'filters') ? ['filters' => $this->filters] : [],
            ...$this->getWidgetData(),
        ]"
        {{-- :data="$this->getWidgetData()" --}}
        :widgets="$visibleWidgets"
    />
</x-filament-panels::page>
