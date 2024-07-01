<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RefreshWidget extends BaseWidget
{
    protected $pagePrefix;

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

    public function removeWidgetCache($pagePrefix)
    {
        Cache::tags($pagePrefix)->flush();
        Cache::forget($pagePrefix."-updated-time");
        Cache::add($pagePrefix."-updated-time",now(auth()->user()->timezone));
        Notification::make()
            ->title('Report Successfully refreshed')
            ->success()
            ->send();

        $this->js("window.location.reload()");
    }
}
