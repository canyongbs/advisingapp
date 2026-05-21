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

use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\CustomerAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerQuestions;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use AdvisingApp\Ai\Models\CustomerAdvisorCategory;
use AdvisingApp\Ai\Models\CustomerAdvisorQuestion;
use AdvisingApp\Ai\Tests\RequestFactories\CustomerAdvisorQuestionRequestFactory;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Features\RenameQnaAdvisorsFeature;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;

test('Create QnA Advisor Question is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-questions', [
                'record' => $customerAdvisor,
            ])
        )
        ->assertForbidden();

    livewire(ManageCustomerQuestions::class, ['record' => $customerAdvisor->getKey()])
        ->assertForbidden();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    actingAs($user)
        ->get(
            CustomerAdvisorResource::getUrl('manage-questions', [
                'record' => $customerAdvisor,
            ])
        )->assertSuccessful();
});

test('can create QnA Advisor Question', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $customerAdvisor = CustomerAdvisor::factory()->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    $customerAdvisorQuestion = collect(CustomerAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: $customerAdvisorQuestion->toArray())
        ->assertHasNoTableActionErrors();

    assertCount(1, CustomerAdvisorQuestion::all());

    assertDatabaseHas(
        CustomerAdvisorQuestion::class,
        $customerAdvisorQuestion->toArray()
    );
});

test('Create QnA Advisor Question validates the inputs', function ($data, $errors) {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.create'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.create']);

    $customerAdvisor = CustomerAdvisor::factory()->create();

    $customerAdvisorQuestion = collect(CustomerAdvisorQuestionRequestFactory::new($data)->create());

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('create', data: $customerAdvisorQuestion->toArray())
        ->assertHasTableActionErrors($errors);

    assertDatabaseMissing(
        CustomerAdvisorQuestion::class,
        $customerAdvisorQuestion->toArray()
    );
})->with(
    [
        'category_id required' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
            ['category_id' => 'required'],
        ],
        'question required' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['question' => null]),
            ['question' => 'required'],
        ],
        'question string' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
            ['question' => 'string'],
        ],
        'question max' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
            ['question' => 'max'],
        ],
        'answer required' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
            ['answer' => 'required'],
        ],
        'answer max' => [
            CustomerAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
            ['answer' => 'max'],
        ],
    ]
);

test('can edit QnA Advisor Question', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();

    // TODO: Cleanup Task - During RenameQnaAdvisorsFeature cleanup, the state can be defined inline again
    $state = RenameQnaAdvisorsFeature::active() ? ['customer_advisor_id' => $customerAdvisor->getKey()] : ['qna_advisor_id' => $customerAdvisor->getKey()];
    $customerAdvisorQuestion = CustomerAdvisorQuestion::factory()->state([
        'category_id' => CustomerAdvisorCategory::factory()->state($state),
    ])->create();

    $request = collect(CustomerAdvisorQuestionRequestFactory::new()->create());

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('edit', record: $customerAdvisorQuestion->getKey(), data: $request->toArray())
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(
        CustomerAdvisorQuestion::class,
        $request->toArray()
    );
});

test('Edit QnA Advisor Question validates the inputs', function ($data, $errors) {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->customerAdvisors = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(RenameQnaAdvisorsFeature::active() ? ['customer_advisor.view-any', 'customer_advisor.*.view', 'customer_advisor.*.update'] : ['qna_advisor.view-any', 'qna_advisor.*.view', 'qna_advisor.*.update']);

    $customerAdvisor = CustomerAdvisor::factory()->create();

    // TODO: Cleanup Task - During RenameQnaAdvisorsFeature cleanup, the state can be defined inline again
    $state = RenameQnaAdvisorsFeature::active() ? ['customer_advisor_id' => $customerAdvisor->getKey()] : ['qna_advisor_id' => $customerAdvisor->getKey()];
    $customerAdvisorQuestion = CustomerAdvisorQuestion::factory()->state([
        'category_id' => CustomerAdvisorCategory::factory()->state($state),
    ])->create();

    $request = CustomerAdvisorQuestionRequestFactory::new($data)->create();

    actingAs($user);

    livewire(ManageCustomerQuestions::class, ['record' => $customerAdvisor->getKey()])
        ->callTableAction('edit', record: $customerAdvisorQuestion->getKey(), data: $request)
        ->assertHasTableActionErrors($errors);
})
    ->with(
        [
            'category_id required' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['category_id' => null]),
                ['category_id' => 'required'],
            ],
            'question required' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['question' => null]),
                ['question' => 'required'],
            ],
            'question string' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['question' => 1]),
                ['question' => 'string'],
            ],
            'question max' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['question' => str()->random(257)]),
                ['question' => 'max'],
            ],
            'answer required' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['answer' => null]),
                ['answer' => 'required'],
            ],
            'answer max' => [
                CustomerAdvisorQuestionRequestFactory::new()->state(['answer' => str()->random(65537)]),
                ['answer' => 'max'],
            ],
        ]
    );
