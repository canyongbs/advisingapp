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
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Enums\ReportAccessKey;
use AdvisingApp\Report\Filament\Pages\Students;
use AdvisingApp\Report\Models\ReportUserAccess;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Field;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

$dateFilterStatePaths = function (Testable $component): array {
    $page = $component->instance();

    assert($page instanceof Students);

    return array_values(array_map(
        fn (Field $field): string => $field->getStatePath(),
        $page->getFiltersForm()->getFlatFields(),
    ));
};

it('keeps the date filters bound to the `filters` state after a population action is applied', function (string $action, bool $selectsGroup) use ($dateFilterStatePaths): void {
    $user = User::factory()->licensed([LicenseType::RetentionCrm])->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::Students->value,
        'user_id' => $user->getKey(),
    ]);

    actingAs($user);

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'user_id' => $user->getKey(),
    ]);

    $component = Livewire::test(Students::class);

    expect($dateFilterStatePaths($component))->toBe(['filters.startDate', 'filters.endDate']);

    $component
        ->callAction(
            TestAction::make($action)->schemaComponent(schema: 'filtersForm'),
            data: $selectsGroup ? ['groupId' => (string) $group->getKey()] : [],
        )
        ->assertHasNoActionErrors();

    expect($dateFilterStatePaths($component))->toBe(['filters.startDate', 'filters.endDate']);

    $component->set('filters.startDate', '2026-01-01');

    expect($dateFilterStatePaths($component))->toBe(['filters.startDate', 'filters.endDate'])
        ->and($component->get('filters.startDate'))->toBe('2026-01-01');
})->with([
    'saved group' => ['selectSavedGroup', true],
    'live filter' => ['buildLiveFilter', false],
]);
