<?php

namespace Assist\Timeline\Filament\Pages;

use Exception;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

abstract class Timeline extends Page
{
    use InteractsWithRecord;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'timeline::page';

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public array $modelsToTimeline = [];

    public Model $currentRecordToView;

    public Model $recordModel;

    public int $recordsPerPage = 3;

    public function loadMoreRecords()
    {
        $this->recordsPerPage += 3;
    }

    public function viewRecord($record, $morphReference)
    {
        $this->currentRecordToView = $this->getRecordFromMorphAndKey($morphReference, $record);

        $this->mountAction('view');
    }

    // TODO Extract this as it's shared between multiple resources at this point
    public function getRecordFromMorphAndKey($morphReference, $key)
    {
        $className = Relation::getMorphedModel($morphReference);

        if (is_null($className)) {
            throw new Exception("Model not found for reference: {$morphReference}");
        }

        return $className::whereKey($key)->firstOrFail();
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView->timeline()->modalViewAction($this->currentRecordToView);
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        if (auth()->user()->cannot('timeline.access')) {
            return false;
        }

        return parent::shouldRegisterNavigation($parameters) && static::getResource()::canView($parameters['record']);
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(auth()->user()->can('timeline.access'), Response::HTTP_FORBIDDEN);

        // TODO We also need to check access for the other entities that are going to be included in the timeline
        // We probably just need to establish that the user can view any of a model, but might need to be more specific
        abort_unless(static::getResource()::canView($this->getRecord()), Response::HTTP_FORBIDDEN);
    }
}
