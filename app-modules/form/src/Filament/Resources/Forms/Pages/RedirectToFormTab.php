<?php

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages;

use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use Filament\Resources\Pages\Page;

class RedirectToFormTab extends Page
{
    protected static string $resource = FormResource::class;

    public function mount(int | string $record): void
    {
        // Notifications sent before the tabs existed link to the former sub-navigation pages, with unscoped submission deep links.
        $tab = last(request()->segments());

        $this->redirect(FormResource::getUrl('view', [
            ...request()->query(),
            'record' => $record,
            'tab' => $tab,
            ...((($tab === 'submissions') && request()->filled('tableAction')) ? ['actionComponent' => 'submissions'] : []),
        ]));
    }
}
