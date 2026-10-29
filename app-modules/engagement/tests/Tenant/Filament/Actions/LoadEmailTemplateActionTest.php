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
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageForm;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Team\Models\Department;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

$loadEmailTemplateAction = fn (): TestAction => TestAction::make('loadEmailTemplate')
    ->schemaComponent('emailAutoReply.body', schema: 'emailAutoReplyForm');

it('finds templates regardless of the case of the search', function () use ($loadEmailTemplateAction): void {
    asSuperAdmin();

    $welcomeTemplate = EmailTemplate::factory()->create(['name' => 'Welcome Email']);
    EmailTemplate::factory()->create(['name' => 'Reminder Email']);

    $form = Form::factory()->create(['is_authenticated' => true]);

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->mountAction($loadEmailTemplateAction())
        ->assertFormFieldExists('emailTemplate', checkFieldUsing: fn (Select $select): bool => array_keys($select->getSearchResults('WELCOME')) === [$welcomeTemplate->getKey()]);
});

it('only lists the templates of the department of the user when filtering by department', function () use ($loadEmailTemplateAction): void {
    $department = Department::factory()->create();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->assignDepartment($department->getKey());
    asSuperAdmin($user);

    $colleague = User::factory()->create();
    $colleague->assignDepartment($department->getKey());

    $ownTemplate = EmailTemplate::factory()->create(['user_id' => $user->getKey()]);
    $colleagueTemplate = EmailTemplate::factory()->create(['user_id' => $colleague->getKey()]);
    EmailTemplate::factory()->create(['user_id' => User::factory()->create()->getKey()]);

    $form = Form::factory()->create(['is_authenticated' => true]);

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->mountAction($loadEmailTemplateAction())
        ->fillForm(['onlyMyDepartmentTemplates' => true])
        ->assertFormFieldExists('emailTemplate', checkFieldUsing: fn (Select $select): bool => collect($select->getOptions())->keys()->sort()->values()->all()
            === collect([$ownTemplate->getKey(), $colleagueTemplate->getKey()])->sort()->values()->all());
});

it('lists no templates when filtering by department for a user without a department', function () use ($loadEmailTemplateAction): void {
    asSuperAdmin();

    EmailTemplate::factory()->create();

    $form = Form::factory()->create(['is_authenticated' => true]);

    Livewire::test(ManageForm::class, ['record' => $form->getRouteKey()])
        ->mountAction($loadEmailTemplateAction())
        ->fillForm(['onlyMyDepartmentTemplates' => true])
        ->assertFormFieldExists('emailTemplate', checkFieldUsing: fn (Select $select): bool => $select->getOptions() === []);
});
