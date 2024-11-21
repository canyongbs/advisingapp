<?php

namespace AdvisingApp\Timeline\Livewire\Concerns;

use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Model;
use App\Actions\GetRecordFromMorphAndKey;
use AdvisingApp\Timeline\Actions\SyncTimelineData;

trait HasTimelineRecords
{
    public array $modelsToTimeline = [];

    public Model $currentRecordToView;

    public Model $recordModel;

    public function mountHasTimelineRecords(): void
    {
        $this->timelineRecords = collect();

        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);

        $this->loadTimelineRecords();
    }

    public function viewRecord($key, $morphReference)
    {
        $this->currentRecordToView = resolve(GetRecordFromMorphAndKey::class)->via($morphReference, $key);

        $this->replaceMountedAction('view');
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView
            ->timeline()
            ->modalViewAction($this->currentRecordToView);
    }
}
