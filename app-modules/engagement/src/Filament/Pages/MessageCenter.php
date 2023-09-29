<?php

namespace Assist\Engagement\Filament\Pages;

use Exception;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class MessageCenter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'engagement::filament.pages.message-center';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 2;

    public ?Educatable $selectedEducatable;

    public Model $currentRecordToView;

    public Collection $subscribedStudentsWithEngagements;

    // TODO Add some seeding specifically for the auth user to get a better view subscribed/engaged students
    public function mount(): void
    {
        // TODO Global loading state

        /** @var User $user */
        $user = auth()->user();

        $subscribedStudentIds =
            $user->subscriptions()
                ->where('subscribable_type', resolve(Student::class)->getMorphClass())
                ->pluck('subscribable_id');

        // TODO We might want to add a scoped relation for engagements for "x" (students, prospects, etc)
        $engagedAndSubscribedStudentIds = $subscribedStudentIds->intersect($user->engagements()->where('recipient_type', resolve(Student::class)->getMorphClass())->pluck('recipient_id'));

        $this->subscribedStudentsWithEngagements =
            Student::whereIn('sisid', $engagedAndSubscribedStudentIds)
                ->join('engagements', 'students.sisid', '=', 'engagements.recipient_id')
                ->where('engagements.user_id', $user->id)
                ->orderBy('engagements.deliver_at', 'desc')
                ->distinct()
                ->select('students.*', 'engagements.deliver_at')
                ->get();

        $this
            ->subscribedStudentsWithEngagements
            ->loadMissing(['engagements' => function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orderBy('deliver_at', 'desc');
            }]);
    }

    public function selectEducatable(string $educatable, string $morphClass): void
    {
        $this->selectedEducatable = $this->getRecordFromMorphAndKey($morphClass, $educatable);
    }

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
