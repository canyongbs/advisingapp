<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Http\Controllers;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\Alert\Presets\AlertPreset;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEventCount;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\StudentDataModel\Models\Scopes\WithoutArchivedStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveySubmission;
use AdvisingApp\Task\Models\Task;
use App\Features\StudentArchivingFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilizationMetricsApiController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsageOfSms = SmsMessage::query()
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        $currentQuotaUsageOfEmail = EmailMessage::query()
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        $alertsByAlertType = collect(AlertPreset::cases())
            ->mapWithKeys(fn (AlertPreset $preset): array => [$preset->value => 0])
            ->replace(
                AlertConfiguration::query()
                    ->selectRaw('alert_configurations.preset, COUNT(student_alerts.sisid) AS alert_count')
                    ->leftJoin('student_alerts', 'student_alerts.alert_configuration_id', '=', 'alert_configurations.id')
                    /*
                     * Raw SQL cannot use the `WithoutArchivedStudents` scope, so the flag is
                     * checked here directly — `archived_at` does not exist until the migration
                     * runs.
                     *
                     * TODO: Cleanup Task (student-archiving): delete this comment block and the
                     * `when()` wrapper below it, chaining its `leftJoin()` and `where()` directly
                     * onto the query. Keep the `deleted_at` comment and filter inside — they are
                     * not part of this feature and must stay once the wrapper is gone.
                     */
                    ->when(StudentArchivingFeature::active(), fn (Builder $query): Builder => $query
                        ->leftJoin('students', 'students.sisid', '=', 'student_alerts.sisid')
                        // `deleted_at` is filtered here too because the enrollment-based alert
                        // presets only exclude deleted enrollments, so a soft-deleted student
                        // with live enrollments still reaches the view. Every other student
                        // metric reads through Eloquent and excludes them.
                        ->where(fn (Builder $query) => $query
                            ->whereNull('student_alerts.sisid')
                            ->orWhere(fn (Builder $query) => $query
                                ->whereNull('students.archived_at')
                                ->whereNull('students.deleted_at'))))
                    ->groupBy('alert_configurations.preset')
                    ->pluck('alert_count', 'alert_configurations.preset')
                    ->map(fn (int|string $count): int => (int) $count)
                    ->all()
            )
            ->all();

        $resourceHubArticlesByCategory = ResourceHubCategory::query()
            ->leftJoin('resource_hub_articles', 'resource_hub_articles.category_id', '=', 'resource_hub_categories.id')
            ->selectRaw('resource_hub_categories.name, COUNT(resource_hub_articles.id) AS article_count')
            ->groupBy('resource_hub_categories.id', 'resource_hub_categories.name')
            ->pluck('article_count', 'resource_hub_categories.name')
            ->map(fn (int|string $count): int => (int) $count)
            ->all();

        $formsSubmittedByMonth = FormSubmission::query()
            ->submitted()
            ->selectRaw("date_trunc('month', submitted_at) as month, COUNT(*) as monthly_total")
            ->groupByRaw("date_trunc('month', submitted_at)")
            ->orderBy('month')
            ->get()
            ->map(function (FormSubmission $item): array {
                $month = (string) data_get($item, 'month');

                return [
                    'month' => CarbonImmutable::parse($month)->format('Y-m'),
                    'count' => (int) data_get($item, 'monthly_total'),
                    'label' => CarbonImmutable::parse($month)->format('Y, F'),
                ];
            })
            ->values()
            ->all();

        $applicationSubmissionsByMonth = ApplicationSubmission::query()
            ->selectRaw("date_trunc('month', created_at) as month, COUNT(*) as monthly_total")
            ->groupByRaw("date_trunc('month', created_at)")
            ->orderBy('month')
            ->get()
            ->map(function (ApplicationSubmission $item): array {
                $month = (string) data_get($item, 'month');

                return [
                    'month' => CarbonImmutable::parse($month)->format('Y-m'),
                    'count' => (int) data_get($item, 'monthly_total'),
                    'label' => CarbonImmutable::parse($month)->format('Y, F'),
                ];
            })
            ->values()
            ->all();

        try {
            return response()->json([
                'data' => [
                    'users' => User::count(),
                    'ai_users' => User::whereRelation('licenses', 'type', LicenseType::ConversationalAi)->count(),
                    'ai_exchanges' => TrackedEventCount::where('type', TrackedEventType::AiExchange)->value('count'),
                    'saved_ai_chats' => AiThread::whereNotNull('name')->whereNotNUll('saved_at')->count(),
                    'saved_prompts' => Prompt::count(),
                    'prompts_inserted' => PromptUse::count(),
                    'retention_crm_users' => User::whereRelation('licenses', 'type', LicenseType::RetentionCrm)->count(),
                    'recruitment_crm_users' => User::whereRelation('licenses', 'type', LicenseType::RecruitmentCrm)->count(),
                    'student_records' => Student::query()->tap(new WithoutArchivedStudents())->count(),
                    'prospect_records' => Prospect::count(),
                    'campaigns' => Campaign::count(),
                    'journey_steps_executed' => CampaignAction::whereNotNull('execution_finished_at')->count(),
                    'tasks' => Task::count(),
                    'concerns' => Concern::count(),
                    'interactions' => Interaction::count(),
                    'groups' => Group::count(),
                    'resource_hub_articles' => ResourceHubArticle::count(),
                    'events_created' => Event::count(),
                    'group_appointments' => BookingGroupAppointment::count(),
                    'personal_appointments' => CalendarEvent::count(),
                    'forms_created' => Form::count(),
                    'forms_submitted' => FormSubmission::count(),
                    'forms_submitted_by_month' => $formsSubmittedByMonth,
                    'applications_submitted_by_month' => $applicationSubmissionsByMonth,
                    'surveys_created' => Survey::count(),
                    'surveys_submitted' => SurveySubmission::count(),
                    'alerts_by_alert_type' => $alertsByAlertType,
                    'resource_hub_articles_by_category' => $resourceHubArticlesByCategory,
                    'emails' => $currentQuotaUsageOfEmail . '/' . $licenseSettings->data->limits->emails,
                    'text_messages' => $currentQuotaUsageOfSms . '/' . $licenseSettings->data->limits->sms,
                    'messages_reset' => $licenseSettings->data->limits->resetDate,
                ],
            ], 200);
        } catch (Exception $exception) {
            report($exception);

            return response()->json([], 500);
        }
    }
}
