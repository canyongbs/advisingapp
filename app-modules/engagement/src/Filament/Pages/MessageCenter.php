<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Filament\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Authenticatable;
use Filament\Actions\ViewAction;
use AdvisingApp\Task\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Prospect\Models\Prospect;
use App\Actions\GetRecordFromMorphAndKey;
use Illuminate\Database\Query\Expression;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Actions\SyncTimelineData;
use Illuminate\Contracts\Database\Eloquent\Builder;
use AdvisingApp\Engagement\Models\EngagementResponse;
use Illuminate\Database\Query\Builder as QueryBuilder;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Timeline\Filament\Pages\Concerns\LoadsTimelineRecords;

class MessageCenter extends Page
{
    use WithPagination;
    use LoadsTimelineRecords;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'engagement::filament.pages.message-center';

    protected static ?string $navigationGroup = 'Engagement Features';

    protected static ?int $navigationSort = 10;

    protected array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public User $user;

    public bool $loadingInbox = true;

    public bool $loadingTimeline = false;

    public ?Educatable $recordModel = null;

    public Model $currentRecordToView;

    public string $emptyStateMessage = 'There are currently no timeline items to show.';

    public string $noMoreRecordsMessage = 'No more timeline items to show.';

    #[Url]
    public string $search = '';

    // TODO Utilize an enum here
    #[Url(as: 'type')]
    public string $filterPeopleType = 'all';

    #[Url(as: 'subscribed')]
    public bool $filterSubscribed = true;

    #[Url(as: 'hasOpenTasks')]
    public bool $filterOpenTasks = false;

    #[Url(as: 'hasOpenServiceRequests')]
    public bool $filterOpenServiceRequests = false;

    #[Url(as: 'startDate')]
    public ?string $filterStartDate = null;

    #[Url(as: 'endDate')]
    public ?string $filterEndDate = null;

    public int $inboxPerPage = 10;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->can('viewAny', Engagement::class)) {
            return false;
        }

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        return $user->can('engagement.view_message_center');
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->user = $user;

        $this->timelineRecords = collect();
    }

    public function updated($property): void
    {
        $filters = [
            'filterPeopleType',
            'filterSubscribed',
            'filterOpenTasks',
            'filterOpenServiceRequests',
            'filterStartDate',
            'filterEndDate',
        ];

        if (in_array($property, $filters)) {
            $this->resetPage('inbox-page');
        }
    }

    public function paginationView()
    {
        return 'engagement::components.pagination';
    }

    public function refreshSelectedEducatable(): void
    {
        $this->loadingTimeline = true;

        $this->reset('initialLoad');
        $this->reset('nextCursor');

        $this->timelineRecords = collect();

        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);

        $this->loadTimelineRecords();

        $this->loadingTimeline = false;
    }

    public function selectEducatable(string $educatable, string $morphClass): void
    {
        $this->loadingTimeline = true;

        $this->dispatch('scroll-to-top');

        $this->reset('initialLoad');
        $this->reset('nextCursor');

        $this->timelineRecords = collect();

        $this->recordModel = resolve(GetRecordFromMorphAndKey::class)->via($morphClass, $educatable);

        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);

        $this->loadTimelineRecords();

        $this->loadingTimeline = false;
    }

    public function selectChanged($value): void
    {
        [$educatableId, $morphClass] = explode(',', $value);

        $this->selectEducatable($educatableId, $morphClass);
    }

    public function viewRecord($key, $morphReference)
    {
        $this->currentRecordToView = resolve(GetRecordFromMorphAndKey::class)->via($morphReference, $key);

        $this->mountAction('view');
    }

    public function viewAction(): ViewAction
    {
        return $this->currentRecordToView->timeline()->modalViewAction($this->currentRecordToView);
    }

    public function getStudentIds(): Collection
    {
        return $this->getEducatableIds(engagementScope: 'sentToStudent', engagementResponseScope: 'sentByStudent');
    }

    public function getProspectIds(): Collection
    {
        return $this->getEducatableIds(engagementScope: 'sentToProspect', engagementResponseScope: 'sentByProspect');
    }

    public function getEducatableIds($engagementScope, $engagementResponseScope): Collection
    {
        $engagementEducatableIds = Engagement::query()
            ->$engagementScope()
            ->hasBeenDelivered()
            ->tap(function (Builder $query) {
                $this->applyFilters(query: $query, dateColumn: 'deliver_at', idColumn: 'recipient_id');
            })
            ->pluck('recipient_id')
            ->unique();

        $engagementResponseEducatableIds = EngagementResponse::query()
            ->$engagementResponseScope()
            ->tap(function (Builder $query) {
                $this->applyFilters(query: $query, dateColumn: 'sent_at', idColumn: 'sender_id');
            })
            ->pluck('sender_id')
            ->unique();

        return $engagementEducatableIds->concat($engagementResponseEducatableIds)->unique();
    }

    public function getLatestActivityForEducatables($ids): QueryBuilder
    {
        $latestEngagementsForEducatables = DB::table('engagements')
            ->whereIn('recipient_id', $ids)
            ->select('recipient_id as educatable_id', DB::raw('MAX(deliver_at) as latest_deliver_at'))
            ->groupBy('educatable_id');

        $latestEngagementResponsesForEducatables = DB::table('engagement_responses')
            ->whereIn('sender_id', $ids)
            ->select('sender_id as educatable_id', DB::raw('MAX(sent_at) as latest_deliver_at'))
            ->groupBy('educatable_id');

        $combinedLatestActivity = $latestEngagementsForEducatables->unionAll($latestEngagementResponsesForEducatables);

        return DB::table(DB::raw("({$combinedLatestActivity->toSql()}) as combined"))
            ->select('educatable_id', DB::raw('MAX(latest_deliver_at) as latest_activity'))
            ->groupBy('educatable_id')
            ->mergeBindings($combinedLatestActivity);
    }

    public function applyFilters(Builder $query, string $dateColumn, string $idColumn): void
    {
        $query
            ->when($this->filterStartDate, function (Builder $query) use ($dateColumn) {
                $query->where($dateColumn, '>=', Carbon::parse($this->filterStartDate));
            })
            ->when($this->filterEndDate, function (Builder $query) use ($dateColumn) {
                $query->where($dateColumn, '<=', Carbon::parse($this->filterEndDate));
            })
            ->when($this->filterSubscribed === true, function (Builder $query) use ($idColumn) {
                $query->whereIn($idColumn, $this->user->subscriptions()->pluck('subscribable_id'));
            })
            ->when($this->filterOpenTasks === true, function (Builder $query) use ($idColumn) {
                $query->whereIn(
                    $idColumn,
                    Task::query()
                        ->open()
                        ->pluck('concern_id')
                );
            })
            ->when($this->filterOpenServiceRequests === true, function (Builder $query) use ($idColumn) {
                $query->whereIn(
                    $idColumn,
                    ServiceRequest::query()
                        ->open()
                        ->pluck('respondent_id')
                );
            });
    }

    public function engage(): void
    {
        $this->mountAction('create');
    }

    public function createAction(): Action
    {
        return SendEngagementAction::make()
            ->educatable($this->recordModel)
            ->after(fn () => $this->refreshSelectedEducatable());
    }

    protected function getViewData(): array
    {
        $this->loadingInbox = true;

        $studentPopulationQuery = null;
        $prospectPopulationQuery = null;

        /** @var Authenticatable $user */
        $user = auth()->user();

        $canAccessStudents = $user->hasLicense(Student::getLicenseType());
        $canAccessProspects = $user->hasLicense(Prospect::getLicenseType());

        if (! ($canAccessStudents && $canAccessProspects)) {
            $this->filterPeopleType = $canAccessStudents ? 'students' : 'prospects';
        }

        if ($this->filterPeopleType === 'students' || $this->filterPeopleType === 'all') {
            $studentIds = $this->getStudentIds();
            $studentLatestActivity = $this->getLatestActivityForEducatables($studentIds);

            $studentPopulationQuery = Student::query()
                ->when($this->search, function ($query, $search) {
                    $query->where(new Expression('lower(full_name)'), 'like', '%' . Str::lower($search) . '%')
                        ->orWhere('sisid', 'like', "%{$search}%")
                        ->orWhere('otherid', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->joinSub($studentLatestActivity, 'latest_activity', function ($join) {
                    $join->on('students.sisid', '=', 'latest_activity.educatable_id');
                })
                ->select('students.sisid', 'students.full_name', 'latest_activity.latest_activity', DB::raw("'student' as type"));
        }

        if ($this->filterPeopleType === 'prospects' || $this->filterPeopleType === 'all') {
            $prospectIds = $this->getProspectIds();
            $prospectLatestActivity = $this->getLatestActivityForEducatables($prospectIds);

            $prospectPopulationQuery = Prospect::query()
                ->when($this->search, function ($query, $search) {
                    $query->where(new Expression('lower(full_name)'), 'like', '%' . Str::lower($search) . '%');
                })
                ->joinSub($prospectLatestActivity, 'latest_activity', function ($join) {
                    $join->on(DB::raw('prospects.id::VARCHAR'), '=', 'latest_activity.educatable_id');
                })
                ->select(DB::raw('prospects.id::VARCHAR'), 'prospects.full_name', 'latest_activity.latest_activity', DB::raw("'prospect' as type"));
        }

        if ($this->filterPeopleType === 'students') {
            $educatables = $studentPopulationQuery;
        } elseif ($this->filterPeopleType === 'prospects') {
            $educatables = $prospectPopulationQuery;
        } else {
            $educatables = $studentPopulationQuery->unionAll($prospectPopulationQuery);
        }

        $this->loadingInbox = false;

        return [
            'educatables' => $educatables->orderBy('latest_activity', 'desc')->paginate($this->inboxPerPage, pageName: 'inbox-page'),
        ];
    }
}
