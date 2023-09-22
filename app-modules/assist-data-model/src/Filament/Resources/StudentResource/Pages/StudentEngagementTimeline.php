<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Models\Contracts\Timelineable;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\Timeline;

class StudentEngagementTimeline extends Timeline
{
    protected static string $resource = StudentResource::class;

    public string $emptyStateMessage = 'There are no engagements to show.';

    // Models to timeline must have a method implemented on the core model, in this case $student
    // In the form of morphnameForTimeline() which returns a collection of the models to timeline
    // Each of these models must also be timelineable - we'll confirm in our iteration
    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public Student $student;

    public function mount($record): void
    {
        $this->student = $this->record = $this->resolveRecord($record);

        // TODO Authorization
        $this->authorizeAccess();

        $this->aggregateRecords = $this->aggregateRecords();
    }

    public function aggregateRecords(): Collection
    {
        $this->aggregateRecords = collect();

        foreach ($this->modelsToTimeline as $model) {
            if (! in_array(Timelineable::class, class_implements($model))) {
                throw new Exception("Model {$model} must implement Timelineable");
            }

            $this->aggregateRecords = $this->aggregateRecords->concat($model::getTimeline($this->student));
        }

        return $this->aggregateRecords = $this->aggregateRecords->sortByDesc(function (Engagement|EngagementResponse $record) {
            return Carbon::parse($record->sortableBy())->timestamp;
        });
    }
}
