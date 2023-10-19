<?php

namespace Assist\Timeline\Filament\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\Page;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use App\Actions\GetRecordFromMorphAndKey;
use Symfony\Component\HttpFoundation\Response;
use Assist\Timeline\Filament\Pages\Concerns\LoadsRecords;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

abstract class TimelinePage extends Page
{
    use InteractsWithRecord;
    use LoadsRecords;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'timeline::page';

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public array $modelsToTimeline = [];

    public Model $currentRecordToView;

    public Model $recordModel;

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

    protected function getViewData(): array
    {
        $timelineRecords = Timeline::query()
            ->forEducatable($this->recordModel)
            ->whereIn(
                'timelineable_type',
                collect($this->modelsToTimeline)->map(fn ($model) => resolve($model)->getMorphClass())->toArray()
            )
            ->orderBy('record_creation', 'desc')
            ->simplePaginate($this->recordsPerPage);

        return [
            'timelineRecords' => $timelineRecords,
        ];
    }
}
