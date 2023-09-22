<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Exception;
use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\Contracts\Timelineable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

// TODO Create a timeline module where this lives
abstract class Timeline extends Page
{
    use InteractsWithRecord;

    protected static string $view = 'engagement::filament.pages.timeline';

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public $aggregateRecords;

    public Model $currentRecordToView;

    public Model $recordModel;

    public function aggregateRecords(): Collection
    {
        $this->aggregateRecords = collect();

        foreach ($this->modelsToTimeline as $model) {
            if (! in_array(Timelineable::class, class_implements($model))) {
                // TODO Custom exception once extracted into timeline module
                throw new Exception("Model {$model} must implement Timelineable");
            }

            $this->aggregateRecords = $this->aggregateRecords->concat($model::getTimeline($this->recordModel));
        }

        return $this->aggregateRecords = $this->aggregateRecords->sortByDesc(function ($record) {
            return Carbon::parse($record->sortableBy())->timestamp;
        });
    }

    public function viewRecord($record, $morphReference)
    {
        $this->currentRecordToView = $this->getRecordFromMorphAndKey($morphReference, $record);

        $this->mountAction('view');
    }

    // TODO Potentially extract this somewhere it can be re-used
    public function getRecordFromMorphAndKey($morphReference, $key)
    {
        $className = Relation::getMorphedModel($morphReference);

        if (is_null($className)) {
            // TODO Custom exception
            throw new Exception("Model not found for reference: {$morphReference}");
        }

        return $className::whereKey($key)->firstOrFail();
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView->modalViewAction();
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        // TODO We also need to check access for the other entities that are going to be included in the timeline
        // This should now be pretty straightforward since we are defining the models in the $modelsToTimeline variable
        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }
}
