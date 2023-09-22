<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

abstract class Timeline extends Page
{
    use InteractsWithRecord;

    protected static string $view = 'engagement::filament.pages.timeline';

    public string $emptyStateMessage = 'There are no records to show on this timeline';

    public $aggregateRecords;

    public Model $currentRecord;

    abstract public function aggregateRecords(): Collection;

    public function viewRecord($record)
    {
        // We need a way to provide some context about this particular record
        // As it theoretically can be any type of record that is timelineable
        // And has been injected into this particular timeline
        // Right now this will simply provide us a UUID, though we may be able to pass in the whole model

        // $this->currentRecord = $record;

        // $this->mountAction('view');
    }

    public function viewAction()
    {
        // TODO Based on the current record
        // We need to determine which view to actually show here
        // Because we need to hook into an infolist for each of them
        // Find a really re-useable way to do this
        // return TaskKanbanViewAction::make()->record($this->currentTask)
        //     ->extraModalFooterActions(
        //         [
        //             EditAction::make('edit')
        //                 ->record($this->currentTask)
        //                 ->form($this->editFormFields())
        //                 ->using(function (Model $record, array $data): Model {
        //                     return app(UpdateTask::class)->handle($record, $data);
        //                 }),
        //         ]
        //     );
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        // TODO We also need to check access for the other entities that are going to be included in the timeline
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }
}
