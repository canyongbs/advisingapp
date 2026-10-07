<?php

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Enums\ApplicationTab;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class RedirectApplicationTab extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ApplicationResource::class;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        assert($this->record instanceof Application);

        $tab = match (str(request()->route()?->getName())->afterLast('.')->toString()) {
            'edit' => ApplicationTab::Edit,
            'manage-application-workflows' => ApplicationTab::Workflows,
            'manage-submissions' => ApplicationTab::Submissions,
            'manage-notifications' => ApplicationTab::Notifications,
            default => abort(404),
        };

        abort_unless($tab->canAccess($this->record), 403);

        $this->redirect(ApplicationResource::getUrl('view', [
            ...request()->query(),
            'record' => $this->record,
            'tab' => $tab->value,
        ]));
    }
}
