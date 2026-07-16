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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\GroupType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Filament\Pages\StudentCaseReport;
use AdvisingApp\Report\Filament\Widgets\StudentCaseStats;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\ReportingFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Schemas\Components\Section;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

function populationAction(string $name): TestAction
{
    return TestAction::make($name)->schemaComponent(schema: 'filtersForm');
}

function actingAsStudentCaseReportUser(): User
{
    ReportingFeature::activate();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->caseManagement = true;
    $settings->save();

    $user = User::factory()->licensed([LicenseType::RetentionCrm])->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::StudentCaseReport->value,
        'user_id' => $user->getKey(),
    ]);

    actingAs($user);

    return $user;
}

function populationLastNameFilters(string $text): array
{
    return [
        'queryBuilder' => [
            'rules' => [
                'C0Cy' => [
                    'type' => 'last',
                    'data' => [
                        'operator' => 'contains',
                        'settings' => [
                            'text' => $text,
                        ],
                    ],
                ],
            ],
        ],
    ];
}

it('resolves the selected group from a saved population payload', function () {
    $widget = new StudentCaseStats();
    $widget->pageFilters = [
        'population' => [
            'type' => 'saved',
            'groupId' => 'the-group-id',
            'liveFilters' => null,
            'model' => 'student',
        ],
    ];

    expect($widget->getSelectedGroup())->toBe('the-group-id');
});

it('resolves a live sentinel from a live population payload', function () {
    $widget = new StudentCaseStats();
    $widget->pageFilters = [
        'population' => [
            'type' => 'live',
            'groupId' => null,
            'liveFilters' => populationLastNameFilters('John'),
            'model' => 'student',
        ],
    ];

    expect($widget->getSelectedGroup())->toBe('live');
});

it('resolves no group when the population payload is empty', function () {
    $widget = new StudentCaseStats();
    $widget->pageFilters = [
        'population' => [
            'type' => null,
            'groupId' => null,
            'liveFilters' => null,
            'model' => 'student',
        ],
    ];

    expect($widget->getSelectedGroup())->toBeNull();
});

it('remains backwards compatible with the legacy populationGroup filter', function () {
    $widget = new StudentCaseStats();
    $widget->pageFilters = [
        'populationGroup' => 'legacy-group-id',
    ];

    expect($widget->getSelectedGroup())->toBe('legacy-group-id');
});

it('applies a live population filter onto a widget query', function () {
    actingAs(User::factory()->licensed(LicenseType::cases())->create());

    Student::factory()->count(3)->create(['last' => 'John']);
    Student::factory()->count(2)->create(['last' => 'Doe']);

    $widget = new StudentCaseStats();
    $widget->pageFilters = [
        'population' => [
            'type' => 'live',
            'groupId' => null,
            'liveFilters' => populationLastNameFilters('John'),
            'model' => 'student',
        ],
    ];

    $query = Student::query();

    $widget->groupFilter($query, $widget->getSelectedGroup());

    expect($query->get()->modelKeys())->toHaveCount(3);
});

it('exposes a live population selection to widgets through the page filters', function () {
    $page = new StudentCaseReport();
    $page->population = ['type' => 'live', 'liveFilters' => populationLastNameFilters('John')];

    $payload = $page->getPopulationPayload();

    expect($payload['type'])->toBe('live')
        ->and($payload['model'])->toBe(GroupModel::Student->value)
        ->and($payload['liveFilters'])->toBe($page->population['liveFilters']);

    expect($page->getPageFilters()['population'])->toBe($payload);
});

it('exposes a saved group selection to widgets through the page filters', function () {
    $page = new StudentCaseReport();
    $page->population = ['type' => 'saved', 'groupId' => 'group-123'];

    $payload = $page->getPopulationPayload();

    expect($payload['type'])->toBe('saved')
        ->and($payload['groupId'])->toBe('group-123')
        ->and($payload['liveFilters'])->toBeNull();

    expect($page->getPageFilters()['population'])->toBe($payload);
});

it('builds the advanced filtering section for group-compatible reports', function () {
    $page = new StudentCaseReport();

    expect($page->getPopulationFilterSection())->toBeInstanceOf(Section::class);
});

it('renders a group-compatible report page with the advanced filtering experience', function () {
    actingAsStudentCaseReportUser();

    get(StudentCaseReport::getUrl())->assertSuccessful();
});

it('opens the saved group slide over', function () {
    actingAsStudentCaseReportUser();

    livewire(StudentCaseReport::class)
        ->mountAction(populationAction('selectSavedGroup'))
        ->assertActionMounted(populationAction('selectSavedGroup'));
});

it('renders the live filter builder inside the slide over', function () {
    actingAsStudentCaseReportUser();

    livewire(StudentCaseReport::class)
        ->mountAction(populationAction('buildLiveFilter'))
        ->assertActionMounted(populationAction('buildLiveFilter'))
        ->assertSuccessful();
});

it('selects a saved group through the population action', function () {
    $user = actingAsStudentCaseReportUser();

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'user_id' => $user->getKey(),
    ]);

    livewire(StudentCaseReport::class)
        ->callAction(populationAction('selectSavedGroup'), data: [
            'groupId' => (string) $group->getKey(),
        ])
        ->assertHasNoActionErrors()
        ->assertSet('population.type', 'saved')
        ->assertSet('population.groupId', (string) $group->getKey());
});

it('applies a live filter through the population action', function () {
    actingAsStudentCaseReportUser();

    $filters = populationLastNameFilters('John');

    livewire(StudentCaseReport::class)
        ->callAction(populationAction('buildLiveFilter'), data: [
            'liveFilters' => $filters,
        ])
        ->assertHasNoActionErrors()
        ->assertSet('population.type', 'live')
        ->assertSet('population.liveFilters', $filters);
});

it('clears the population selection', function () {
    $user = actingAsStudentCaseReportUser();

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'user_id' => $user->getKey(),
    ]);

    livewire(StudentCaseReport::class)
        ->set('population', ['type' => 'saved', 'groupId' => (string) $group->getKey()])
        ->callAction(populationAction('clearPopulation'))
        ->assertSet('population', null);
});

it('saves a live filter as a group', function () {
    actingAsStudentCaseReportUser();

    $filters = populationLastNameFilters('John');

    livewire(StudentCaseReport::class)
        ->set('population', ['type' => 'live', 'liveFilters' => $filters])
        ->callAction(populationAction('saveAsGroup'), data: [
            'name' => 'My saved live filter',
        ])
        ->assertHasNoActionErrors()
        ->assertSet('population.type', 'saved');

    $group = Group::query()->where('name', 'My saved live filter')->first();

    expect($group)->not->toBeNull()
        ->and($group->model)->toBe(GroupModel::Student)
        ->and($group->type)->toBe(GroupType::Dynamic)
        ->and($group->filters)->toBe($filters);
});
