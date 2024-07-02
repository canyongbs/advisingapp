<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Livewire\Attributes\On;

class RefreshWidget extends BaseWidget
{
    protected $pagePrefix;

    protected $updatedTime;

    protected static string $view = 'report::filament.pages.report-refresh-widgets';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount($pagePrefix = '')
    {
        $this->pagePrefix = $pagePrefix;
    }
    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        Cache::tags([$this->pagePrefix])->add('updated-time', now());
        $this->dispatch('$refresh');

    }
    public function removeWidgetCache($pagePrefix)
    {
        Cache::tags($pagePrefix)->flush();
        Notification::make()
            ->title('Report Successfully refreshed' . now())
            ->success()
            ->send();

        $this->dispatch('refresh-widgets');
    }
}
