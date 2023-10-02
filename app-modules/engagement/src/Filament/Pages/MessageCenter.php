<?php

namespace Assist\Engagement\Filament\Pages;

use Exception;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Timeline\Actions\AggregatesTimelineRecordsForModel;

class MessageCenter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'engagement::filament.pages.message-center';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 2;

    protected array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public User $user;

    public bool $loadingInbox = true;

    public bool $loadingTimeline = false;

    public Collection $educatables;

    public Collection $subscribedStudentsWithEngagements;

    public ?Educatable $selectedEducatable;

    public Collection $aggregateRecords;

    public Model $currentRecordToView;

    public function mount(): void
    {
        /** @var User $user */
        $this->user = auth()->user();

        $subscribedEducatableIds =
            $this->user->subscriptions()
                // For now, we are just operating on Students
                ->toStudents()
                ->pluck('subscribable_id');

        $engagedAndSubscribedEducatablesIds =
            $subscribedEducatableIds->intersect(
                $this->user->engagements()
                    // Again, we are only operating on Students for now
                    ->sentToStudent()
                    ->pluck('recipient_id')
            );

        // TODO Update this - the latest engagement needs to be either the latest outgoing engagement or incoming engagement response...
        $latestEngagements = DB::table('engagements')
            ->select('recipient_id', DB::raw('MAX(deliver_at) as latest_deliver_at'))
            ->where('user_id', $this->user->id)
            ->whereIn('recipient_id', $engagedAndSubscribedEducatablesIds)
            ->groupBy('recipient_id');

        $this->subscribedStudentsWithEngagements = Student::whereIn('students.sisid', $engagedAndSubscribedEducatablesIds)
            ->joinSub($latestEngagements, 'latest_engagements', function ($join) {
                $join->on('students.sisid', '=', 'latest_engagements.recipient_id');
            })
            ->orderBy('latest_engagements.latest_deliver_at', 'desc')
            ->select('students.*', 'latest_engagements.latest_deliver_at')
            ->get();

        // TODO This is where we'd also add prospects into the fold
        $this->educatables = $this->subscribedStudentsWithEngagements;

        $this->loadingInbox = false;
    }

    public function selectEducatable(string $educatable, string $morphClass): void
    {
        $this->loadingTimeline = true;

        $this->selectedEducatable = $this->getRecordFromMorphAndKey($morphClass, $educatable);

        $this->aggregateRecords = resolve(AggregatesTimelineRecordsForModel::class)->handle($this->selectedEducatable, $this->modelsToTimeline);

        $this->loadingTimeline = false;
    }

    // TODO Extract this away... This is used in multiple places
    public function getRecordFromMorphAndKey($morphReference, $key)
    {
        $className = Relation::getMorphedModel($morphReference);

        if (is_null($className)) {
            throw new Exception("Model not found for reference: {$morphReference}");
        }

        return $className::whereKey($key)->firstOrFail();
    }

    // TODO This currently won't work as we aren't actually in the context
    // Of the timeline and we don't have access to the view actions that we need
    public function viewRecord($record, $morphReference)
    {
        $this->currentRecordToView = $this->getRecordFromMorphAndKey($morphReference, $record);

        $this->mountAction('view');
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView->timeline()->modalViewAction($this->currentRecordToView);
    }
}
