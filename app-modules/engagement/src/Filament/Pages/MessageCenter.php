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

    public $educatables;

    public $subscribedStudentsWithEngagements;

    public ?Educatable $selectedEducatable;

    public Collection $aggregateRecords;

    public Model $currentRecordToView;

    public string $search = '';

    public function mount(): void
    {
        /** @var User $user */
        $this->user = auth()->user();

        $this->getData();
    }

    public function updating(): void
    {
        $this->getData();
    }

    public function hydrate(): void
    {
        $this->getData();
    }

    // TODO I don't think this is the best way to accomplish this
    // But for some reason, the latest_activity is not being persisted
    // Across the updated lifecycle hook, so we need to re-hydrate the data
    public function getData(): void
    {
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

        $latestEngagements = DB::table('engagements')
            ->select('recipient_id as educatable_id', DB::raw('MAX(deliver_at) as latest_deliver_at'))
            ->where('user_id', $this->user->id)
            ->whereIn('recipient_id', $engagedAndSubscribedEducatablesIds)
            ->groupBy('recipient_id');

        $latestEngagementResponses = DB::table('engagement_responses')
            ->select('sender_id as educatable_id', DB::raw('MAX(sent_at) as latest_deliver_at'))
            ->whereIn('sender_id', $engagedAndSubscribedEducatablesIds)
            ->groupBy('sender_id');

        $combinedEngagements = $latestEngagements->unionAll($latestEngagementResponses);

        $latestActivityForStudents = DB::table(DB::raw("({$combinedEngagements->toSql()}) as combined"))
            ->select('educatable_id', DB::raw('MAX(latest_deliver_at) as latest_activity'))
            ->groupBy('educatable_id')
            ->mergeBindings($combinedEngagements);

        $this->subscribedStudentsWithEngagements = Student::query()
            ->when($this->search, function ($query, $search) {
                $query->where('first', 'like', "%{$search}%")
                    ->orWhere('last', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            })
            ->whereIn('students.sisid', $engagedAndSubscribedEducatablesIds)
            ->leftJoinSub($latestActivityForStudents, 'latest_activity', function ($join) {
                $join->on('students.sisid', '=', 'latest_activity.educatable_id');
            })
            ->select('students.*', 'latest_activity.latest_activity')
            ->orderBy('latest_activity.latest_activity', 'desc')
            ->get();

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

    public function selectChanged($value)
    {
        [$educatableId, $morphClass] = explode(',', $value);

        $this->selectEducatable($educatableId, $morphClass);
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
