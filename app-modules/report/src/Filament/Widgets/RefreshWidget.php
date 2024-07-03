<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RefreshWidget extends BaseWidget
{
    public $pagePrefix;

    protected $updatedTime;

    protected static string $view = 'report::filament.pages.report-refresh-widgets';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount($pagePrefix = 'test')
    {
        $this->pagePrefix = $pagePrefix;
    }

    public function removeWidgetCache($pagePrefix)
    {
        Cache::tags($pagePrefix)->flush();
        Notification::make()
            ->title('Report Successfully refreshed')
            ->success()
            ->send();
        Cache::tags([$pagePrefix])->put('updated-time', now());
        $this->dispatch('refresh-widgets');
    }
}
