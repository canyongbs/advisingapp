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

use App\Models\User;

use function Pest\Laravel\get;

use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Task\Models\Task;
use App\Settings\LicenseSettings;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Segment\Models\Segment;
use App\Http\Middleware\CheckOlympusKey;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\MeetingCenter\Models\Event;

use function Pest\Laravel\withoutMiddleware;

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Survey\Models\SurveySubmission;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Models\TrackedEventCount;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;

beforeEach(function () {
    withoutMiddleware(CheckOlympusKey::class);
});

it('checks the API returns users', function () {
    $randomRecords = random_int(1, 10);

    User::factory()->count($randomRecords)->create();

    $softDeleteUser = User::factory()->create();
    $softDeleteUser->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['users'])->toBe($randomRecords);
});

it('checks the API returns AI Users', function () {
    $randomRecords = random_int(1, 10);

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->conversationalAiSeats = $randomRecords + 1;
    $licenseSettings->save();

    User::factory()->count($randomRecords)->licensed([LicenseType::ConversationalAi])->create();

    $softDeleteAiUser = User::factory()->licensed([LicenseType::ConversationalAi])->create();
    $softDeleteAiUser->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['ai_users'])->toBe($randomRecords);
});

it('checks the API returns AI Exchanges', function () {
    $randomRecords = 1;

    TrackedEventCount::factory()->count($randomRecords)->create([
        'type' => TrackedEventType::AiExchange,
    ]);

    TrackedEventCount::factory()->count($randomRecords)->create([
        'type' => TrackedEventType::AiThreadSaved,
    ]);

    $totalExchanges = TrackedEventCount::where('type', TrackedEventType::AiExchange)->sum('count');

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['ai_exchanges'])->toBe($totalExchanges);
});

it('checks the API returns Saved AI Chats', function () {
    $randomRecords = random_int(1, 10);

    AiThread::factory()->count($randomRecords)->saved()->create();

    $softDeleteAiThread = AiThread::factory()->saved()->create();
    $softDeleteAiThread->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['saved_ai_chats'])->toBe($randomRecords);
});

it('checks the API returns Saved Prompts', function () {
    $randomRecords = random_int(1, 10);

    Prompt::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['saved_prompts'])->toBe($randomRecords);
});

it('checks the API returns Prompts Inserted', function () {
    $randomRecords = random_int(1, 10);

    PromptUse::factory()->count($randomRecords)->create();

    $softDeletePromptUse = PromptUse::factory()->create();
    $softDeletePromptUse->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['prompts_inserted'])->toBe($randomRecords);
});

it('checks the API returns Retention CRM Users', function () {
    $randomRecords = random_int(1, 10);

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->retentionCrmSeats = $randomRecords + 1;
    $licenseSettings->save();

    User::factory()->count($randomRecords)->licensed([LicenseType::RetentionCrm])->create();

    $softDeleteRetentionCrmUser = User::factory()->licensed([LicenseType::RetentionCrm])->create();
    $softDeleteRetentionCrmUser->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['retention_crm_users'])->toBe($randomRecords);
});

it('checks the API returns Recruitment CRM Users', function () {
    $randomRecords = random_int(1, 10);

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->recruitmentCrmSeats = $randomRecords + 1;
    $licenseSettings->save();

    User::factory()->count($randomRecords)->licensed([LicenseType::RecruitmentCrm])->create();

    $softDeleteRecruitmentCrmUser = User::factory()->licensed([LicenseType::RecruitmentCrm])->create();
    $softDeleteRecruitmentCrmUser->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['recruitment_crm_users'])->toBe($randomRecords);
});

it('checks the API returns Student Records', function () {
    $randomRecords = random_int(1, 10);

    $totalRecords = $randomRecords + (Student::count());

    Student::factory()->count($randomRecords)->create();

    $softDeleteStudent = Student::factory()->create();
    $softDeleteStudent->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['student_records'])->toBe($totalRecords);
});

it('checks the API returns Prospects Records', function () {
    $randomRecords = random_int(1, 10);

    Prospect::factory()->count($randomRecords)->create();

    $softDeleteProspect = Prospect::factory()->create();
    $softDeleteProspect->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['prospect_records'])->toBe($randomRecords);
});

it('checks the API returns Campaigns', function () {
    $randomRecords = random_int(1, 10);

    Campaign::factory()->count($randomRecords)->create();

    $softDeleteCampaign = Campaign::factory()->create();
    $softDeleteCampaign->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['campaigns'])->toBe($randomRecords);
});

it('checks the API returns Journey Steps Executed', function () {
    $randomRecords = random_int(1, 10);

    CampaignAction::factory()->count($randomRecords)->create();

    $softDeleteCampaignAction = CampaignAction::factory()->successfulExecution()->create();
    $softDeleteCampaignAction->delete();

    CampaignAction::factory()->count($randomRecords)->successfulExecution()->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['journey_steps_executed'])->toBe($randomRecords);
});

it('checks the API returns Tasks', function () {
    $randomRecords = random_int(1, 10);

    Task::factory()->count($randomRecords)->create();

    $softDeleteTask = Task::factory()->create();
    $softDeleteTask->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['tasks'])->toBe($randomRecords);
});

it('checks the API returns Alerts', function () {
    $randomRecords = random_int(1, 10);

    Alert::factory()->count($randomRecords)->create();

    $softDeleteAlert = Alert::factory()->create();
    $softDeleteAlert->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['alerts'])->toBe($randomRecords);
});

it('checks the API returns Segments', function () {
    $randomRecords = random_int(1, 10);

    Segment::factory()->count($randomRecords)->create();

    $softDeleteSegment = Segment::factory()->create();
    $softDeleteSegment->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['segments'])->toBe($randomRecords);
});

it('checks the API returns Resource Hub Articles', function () {
    $randomRecords = random_int(1, 10);

    ResourceHubArticle::factory()->count($randomRecords)->create();

    $softDeleteResourceHubArticle = ResourceHubArticle::factory()->create();
    $softDeleteResourceHubArticle->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['resource_hub_articles'])->toBe($randomRecords);
});

it('checks the API returns Events Created', function () {
    $randomRecords = random_int(1, 10);

    Event::factory()->count($randomRecords)->create();

    $softDeleteEvent = Event::factory()->create();
    $softDeleteEvent->delete();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['events_created'])->toBe($randomRecords);
});

it('checks the API returns Forms Created', function () {
    $randomRecords = random_int(1, 10);

    Form::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['forms_created'])->toBe($randomRecords);
});

it('checks the API returns Forms Submitted', function () {
    $randomRecords = random_int(1, 10);

    FormSubmission::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['forms_submitted'])->toBe($randomRecords);
});

it('checks the API returns Surveys Created', function () {
    $randomRecords = random_int(1, 10);

    $totalRecords = $randomRecords + (Survey::count());

    Survey::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['surveys_created'])->toBe($totalRecords);
});

it('checks the API returns Surveys Submitted', function () {
    SurveySubmission::truncate();

    $randomRecords = random_int(1, 10);

    SurveySubmission::factory()->count($randomRecords)->create();

    $response = get('/api/utilization-metrics');

    $data = $response->json('data');

    $response->assertStatus(200);

    expect($data['surveys_submitted'])->toBe($randomRecords);
});
