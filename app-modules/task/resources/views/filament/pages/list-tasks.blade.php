@php
    use Assist\Livewire\Task\TaskKanban;use Filament\Support\Facades\FilamentView;
@endphp
<x-filament-panels::page
        @class([
            'fi-resource-list-records-page',
            'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        ])
>
    <div class="w-full flex justify-start">
        <div class="grid max-w-xs grid-cols-2 gap-1 p-1 bg-gray-100 rounded-lg dark:bg-gray-800" role="group">
            <button
                    type="button"
                    @class([
                        'px-5 py-1.5 text-xs font-medium rounded-lg',
                        'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' => $viewType === 'table',
                        'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' => $viewType !== 'table',
                    ])
                    wire:click="setViewType('table')"
            >
                <x-filament::icon class="w-6 h-6" icon="heroicon-m-table-cells"/>
            </button>
            <button
                    type="button"
                    @class([
                        'px-5 py-1.5 text-xs font-medium rounded-lg',
                        'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' => $viewType === 'kanban',
                        'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' => $viewType !== 'kanban',
                    ])
                    wire:click="setViewType('kanban')"
            >
                <x-filament::icon class="w-6 h-6" icon="heroicon-m-view-columns"/>
            </button>
        </div>
    </div>
    @if($viewType === 'table')
        <div class="flex flex-col gap-y-6">
            @if (count($tabs = $this->getTabs()))
                <x-filament::tabs>
                    {{ FilamentView::renderHook('panels::resource.pages.list-records.tabs.start', scopes: $this->getRenderHookScopes()) }}

                    @foreach ($tabs as $tabKey => $tab)
                        @php
                            $activeTab = strval($activeTab);
                            $tabKey = strval($tabKey);
                        @endphp

                        <x-filament::tabs.item
                                :active="$activeTab === $tabKey"
                                :badge="$tab->getBadge()"
                                :icon="$tab->getIcon()"
                                :icon-position="$tab->getIconPosition()"
                                :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                        >
                            {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                        </x-filament::tabs.item>
                    @endforeach

                    {{ FilamentView::renderHook('panels::resource.pages.list-records.tabs.end', scopes: $this->getRenderHookScopes()) }}
                </x-filament::tabs>
            @endif

            {{ FilamentView::renderHook('panels::resource.pages.list-records.table.before', scopes: $this->getRenderHookScopes()) }}

            {{ $this->table }}

            {{ FilamentView::renderHook('panels::resource.pages.list-records.table.after', scopes: $this->getRenderHookScopes()) }}
        </div>
    @elseif($viewType === 'kanban')
        <livewire:task-kanban />
    @endif
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kanban', ($wire) => ({
                init() {
                    const kanbanLists = document.querySelectorAll('[id^="kanban-list-"]');

                    kanbanLists.forEach(kanbanList => {
                        window.Sortable.create(kanbanList, {
                            group: 'kanban',
                            animation: 100,
                            forceFallback: true,
                            dragClass: 'drag-card',
                            ghostClass: 'ghost-card',
                            easing: 'cubic-bezier(0, 0.55, 0.45, 1)',
                            onAdd: async function (evt) {
                                try {
                                    const result = await $wire.movedTask(evt.item.dataset.task, evt.from.dataset.status, evt.to.dataset.status);

                                    if (result.original.success) {
                                        new FilamentNotification()
                                            .icon('heroicon-o-check-circle')
                                            .title(result.original.message)
                                            .iconColor('success')
                                            .send()
                                    } else {
                                        new FilamentNotification()
                                            .icon('heroicon-o-x-circle')
                                            .title(result.original.message)
                                            .iconColor('danger')
                                            .send()
                                    }
                                } catch (e) {
                                    new FilamentNotification()
                                        .icon('heroicon-o-x-circle')
                                        .title('Something went wrong, please try again later')
                                        .iconColor('danger')
                                        .send()
                                }
                            },
                        });
                    });
                },
            }))
        })
    </script>
</x-filament-panels::page>
