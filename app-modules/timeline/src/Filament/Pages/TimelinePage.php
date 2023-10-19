<?php

namespace Assist\Timeline\Filament\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\Page;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use App\Actions\GetRecordFromMorphAndKey;
use Assist\Timeline\Actions\SyncTimelineData;
use Symfony\Component\HttpFoundation\Response;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Assist\Timeline\Filament\Pages\Concerns\LoadsTimelineRecords;

abstract class TimelinePage extends Page
{
    use InteractsWithRecord;
    use LoadsTimelineRecords;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'timeline::page';

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public string $noMoreRecordsMessage = 'You have reached the end of this timeline.';

    public array $modelsToTimeline = [];

    public Model $currentRecordToView;

    public Model $recordModel;

    public function mount($record): void
    {
        $this->recordModel = $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->timelineRecords = collect();

        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);

        $this->loadTimelineRecords();
    }

    public function viewRecord($key, $morphReference)
    {
        $this->currentRecordToView = resolve(GetRecordFromMorphAndKey::class)->via($morphReference, $key);

        $this->mountAction('view');
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView
            ->timeline()
            ->modalViewAction($this->currentRecordToView);
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
