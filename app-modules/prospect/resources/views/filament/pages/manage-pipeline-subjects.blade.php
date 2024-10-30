<x-filament-panels::page @class([
    'fi-resource-manage-related-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div class="flex w-full justify-start">
        <div
            class="grid max-w-xs grid-cols-2 gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800"
            role="group"
        >
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' =>
                        $viewType === 'table',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' =>
                        $viewType !== 'table',
                ])
                wire:click="setViewType('table')"
            >
                <x-filament::icon
                    class="h-6 w-6"
                    icon="heroicon-m-table-cells"
                />
            </button>
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' =>
                        $viewType === 'kanban',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' =>
                        $viewType !== 'kanban',
                ])
                wire:click="setViewType('kanban')"
            >
                <x-filament::icon
                    class="h-6 w-6"
                    icon="heroicon-m-view-columns"
                />
            </button>
        </div>
    </div>

    @if ($viewType === 'table')

        @if ($this->table->getColumns())
            <div class="flex flex-col gap-y-6">
                <x-filament-panels::resources.tabs />

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->table }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>
        @endif

        @if (count($relationManagers = $this->getRelationManagers()))
            <x-filament-panels::resources.relation-managers
                :active-locale="isset($activeLocale) ? $activeLocale : null"
                :active-manager="$this->activeRelationManager ?? array_key_first($relationManagers)"
                :managers="$relationManagers"
                :owner-record="$record"
                :page-class="static::class"
            />
        @endif
    @elseif($viewType === 'kanban')
        @livewire('prospect-pipeline-kanban', [
            'pipeline' => $this->getOwnerRecord(),
        ])
        <x-filament-actions::modals />
    @endif
    <script src="{{ url('js/canyon-gbs/prospect-pipeline/kanban.js') }}"></script>
</x-filament-panels::page>
