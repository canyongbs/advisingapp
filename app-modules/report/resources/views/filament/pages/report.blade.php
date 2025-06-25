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

    {{ $this->filtersForm }}

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="$this->getWidgetData()"
        :widgets="$visibleWidgets"
    />
</x-filament-panels::page>
