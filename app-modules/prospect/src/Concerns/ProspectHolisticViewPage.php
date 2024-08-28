<?php

namespace AdvisingApp\Prospect\Concerns;

use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Support\Facades\FilamentView;

trait ProspectHolisticViewPage
{
    public function boot()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn (): View => view('prospect::student-converted-badge')
        );
    }
}
