
@use("AdvisingApp\Ai\Enums\QnaAdvisorReportTableTab")
<div>
    <x-filament::tabs label="Content tabs">
           @foreach (QnaAdvisorReportTableTab::cases() as $tab)
               <x-filament::tabs.item
                   wire:click="$set('activeTab', '{{ $tab->value }}')"
                   :active="$activeTab === $tab->value"
               >
                   {{ $tab->getLabel() }}
               </x-filament::tabs.item>
           @endforeach
       </x-filament::tabs>
   <x-filament-widgets::widget class="fi-wi-table">
       {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_START, scopes: static::class) }}

       {{ $this->table }}

       {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\Widgets\View\WidgetsRenderHook::TABLE_WIDGET_END, scopes: static::class) }}
   </x-filament-widgets::widget>
</div>
