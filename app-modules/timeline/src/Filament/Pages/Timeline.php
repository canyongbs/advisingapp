<?php

namespace Assist\Timeline\Filament\Pages;

use Exception;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Timeline\Exceptions\ModelMustHaveATimeline;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

abstract class Timeline extends Page
{
    use InteractsWithRecord;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'timeline::timeline';

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public $aggregateRecords;

    public array $modelsToTimeline = [];

    public Model $currentRecordToView;

    public Model $recordModel;

    public function aggregateRecords(): Collection
    {
        $this->aggregateRecords = collect();

        foreach ($this->modelsToTimeline as $model) {
            if (! in_array(ProvidesATimeline::class, class_implements($model))) {
                throw new ModelMustHaveATimeline("Model {$model} must have a timeline available");
            }

            $this->aggregateRecords = $this->aggregateRecords->concat($model::getTimelineData($this->recordModel));
        }

        return $this->aggregateRecords = $this->aggregateRecords->sortByDesc(function ($record) {
            return Carbon::parse($record->timeline()->sortableBy())->timestamp;
        });
    }

    public function viewRecord($record, $morphReference)
    {
        $this->currentRecordToView = $this->getRecordFromMorphAndKey($morphReference, $record);

        $this->mountAction('view');
    }

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
        return parent::shouldRegisterNavigation($parameters) && static::getResource()::canEdit($parameters['record']);
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        // TODO We also need to check access for the other entities that are going to be included in the timeline
        // We probably just need to establish that the user can view any of a model, but might need to be more specific
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }
}
