<?php

namespace Assist\Engagement\Filament\Pages;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Filament\Pages\Page;
use Assist\Task\Models\Task;
use Livewire\WithPagination;
use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Timeline\Actions\AggregatesTimelineRecordsForModel;

class MessageCenter extends Page
{
    use WithPagination;

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

    public ?Educatable $selectedEducatable;

    public Collection $aggregateRecordsForEducatable;

    public Model $currentRecordToView;

    public string $search = '';

    // TODO students, prospects, all
    public string $peopleScope = 'students';

    public bool $filterSubscribed = true;

    public bool $filterOpenTasks = false;

    public bool $filterOpenServiceRequests = false;

    public ?string $filterStartDate = null;

    public ?string $filterEndDate = null;

    public array $paginationOptions = [
        10,
        25,
        50,
    ];

    public int $pagination = 10;

    public function mount(): void
    {
        /** @var User $user */
        $this->user = auth()->user();
    }

    public function selectEducatable(string $educatable, string $morphClass): void
    {
        $this->loadingTimeline = true;

        $this->selectedEducatable = $this->getRecordFromMorphAndKey($morphClass, $educatable);

        $this->aggregateRecordsForEducatable = resolve(AggregatesTimelineRecordsForModel::class)->handle($this->selectedEducatable, $this->modelsToTimeline);

        $this->loadingTimeline = false;
    }

    public function selectChanged($value): void
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

    // TODO Remove this if we don't make Filters extractable similar to Filament...
    public function getFilters(): array
    {
        return [
        ];
    }

    public function filtersAreApplied(): bool
    {
        return $this->filterSubscribed === true || $this->filterOpenTasks === true || $this->filterOpenServiceRequests === true;
    }

    public function getEducatableIds(): Collection
    {
        // Filters we need to support
        // Person Type (select - student or prospect) :check:
        // Date range (start and end date) :check:
        // Subscribed (checkbox - defaulted to true) :check:
        // Open Tasks (checkbox - defaulted to false) :check:
        // Open Service Requests (checkbox - defaulted to false) :check:

        $engagementEducatableIds = Engagement::query()
            ->when($this->peopleScope, function (Builder $query) {
                match ($this->peopleScope) {
                    'students' => $query->sentToStudent(),
                    'prospects' => $query->sentToProspect(),
                    'all' => $query->sentToStudent()->sentToProspect(),
                };
            })
            ->when($this->filterStartDate, function (Builder $query) {
                $query->where('deliver_at', '>=', Carbon::parse($this->filterStartDate));
            })
            ->when($this->filterEndDate, function (Builder $query) {
                $query->where('deliver_at', '<=', Carbon::parse($this->filterEndDate));
            })
            ->when($this->filterSubscribed === true, function (Builder $query) {
                $query->whereIn('recipient_id', $this->user->subscriptions()->pluck('subscribable_id'));
            })
            ->when($this->filterOpenTasks === true, function (Builder $query) {
                $query->whereIn(
                    'recipient_id',
                    Task::query()
                        ->open()
                        ->pluck('concern_id')
                );
            })
            ->when($this->filterOpenServiceRequests === true, function (Builder $query) {
                $query->whereIn(
                    'recipient_id',
                    ServiceRequest::query()
                        ->open()
                        ->pluck('respondent_id')
                );
            })
            ->pluck('recipient_id')
            ->unique();

        $engagementResponseEducatableIds = EngagementResponse::query()
            ->when($this->peopleScope, function (Builder $query) {
                match ($this->peopleScope) {
                    'students' => $query->sentByStudent(),
                    'prospects' => $query->sentByProspect(),
                    'all' => $query->sentByStudent()->orWhere(function (Builder $query) {
                        $query->sentByProspect();
                    }),
                };
            })
            ->when($this->filterStartDate, function (Builder $query) {
                $query->where('sent_at', '>=', Carbon::parse($this->filterStartDate));
            })
            ->when($this->filterEndDate, function (Builder $query) {
                $query->where('sent_at', '<=', Carbon::parse($this->filterEndDate));
            })
            ->when($this->filterSubscribed === true, function (Builder $query) {
                $query->whereIn('sender_id', $this->user->subscriptions()->pluck('subscribable_id'));
            })
            ->when($this->filterOpenTasks === true, function (Builder $query) {
                $query->whereIn(
                    'sender_id',
                    Task::query()
                        ->open()
                        ->pluck('concern_id')
                );
            })
            ->when($this->filterOpenServiceRequests === true, function (Builder $query) {
                $query->whereIn(
                    'sender_id',
                    ServiceRequest::query()
                        ->open()
                        ->pluck('respondent_id')
                );
            })
            ->pluck('sender_id')
            ->unique();

        $educatableIds = $engagementEducatableIds->concat($engagementResponseEducatableIds)->unique();

        return $educatableIds;
    }

    protected function getViewData(): array
    {
        $this->loadingInbox = true;

        $educatableIds = $this->getEducatableIds();

        $latestEngagementsForEducatables = DB::table('engagements')
            ->select('recipient_id as educatable_id', DB::raw('MAX(deliver_at) as latest_deliver_at'))
            ->whereIn('recipient_id', $educatableIds)
            ->groupBy('recipient_id');

        $latestEngagementResponsesForEducatables = DB::table('engagement_responses')
            ->select('sender_id as educatable_id', DB::raw('MAX(sent_at) as latest_deliver_at'))
            ->whereIn('sender_id', $educatableIds)
            ->groupBy('sender_id');

        $combinedEngagements = $latestEngagementsForEducatables->unionAll($latestEngagementResponsesForEducatables);

        $latestActivityForStudents = DB::table(DB::raw("({$combinedEngagements->toSql()}) as combined"))
            ->select('educatable_id', DB::raw('MAX(latest_deliver_at) as latest_activity'))
            ->groupBy('educatable_id')
            ->mergeBindings($combinedEngagements);

        // TODO We also need to be pulling from the Prospect population here...
        $studentPopulation = Student::query()
            ->when($this->search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('sisid', 'like', "%{$search}%")
                    ->orWhere('otherid', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->whereIn('students.sisid', $educatableIds)
            ->joinSub($latestActivityForStudents, 'latest_activity', function ($join) {
                $join->on('students.sisid', '=', 'latest_activity.educatable_id');
            })
            ->select('students.*', 'latest_activity.latest_activity')
            ->orderBy('latest_activity.latest_activity', 'desc')
            ->paginate($this->pagination);

        $educatables = $studentPopulation;

        $this->loadingInbox = false;

        return [
            'educatables' => $educatables,
        ];
    }
}
