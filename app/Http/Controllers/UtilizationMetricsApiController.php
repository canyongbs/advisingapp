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

namespace App\Http\Controllers;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEventCount;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveySubmission;
use AdvisingApp\Task\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UtilizationMetricsApiController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
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
                    'student_records' => Student::count(),
                    'prospect_records' => Prospect::count(),
                    'campaigns' => Campaign::count(),
                    'journey_steps_executed' => CampaignAction::whereNotNull('successfully_executed_at')->count(),
                    'tasks' => Task::count(),
                    'alerts' => Alert::count(),
                    'segments' => Segment::count(),
                    'resource_hub_articles' => ResourceHubArticle::count(),
                    'events_created' => Event::count(),
                    'forms_created' => Form::count(),
                    'forms_submitted' => FormSubmission::count(),
                    'surveys_created' => Survey::count(),
                    'surveys_submitted' => SurveySubmission::count(),
                ],
            ], 200);
        } catch (Exception $e) {
            report($e);

            return response()->json([
            ], 500);
        }
    }
}
