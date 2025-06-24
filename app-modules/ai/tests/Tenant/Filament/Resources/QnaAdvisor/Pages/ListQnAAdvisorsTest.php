<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ListQnaAdvisors;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('ListQnaAdvisors is gated with proper access control', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('index')
        )->assertSuccessful();
});

it('render QnA Advisors default to without archived', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view']);

    actingAs($user);

    $qnaAdvisors = QnaAdvisor::factory()->count(3)->state([
        'archived_at' => null,
    ])->create();

    $archivedQnaAdvisors = QnaAdvisor::factory()->count(3)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListQnaAdvisors::class)
        ->assertCanSeeTableRecords($qnaAdvisors)
        ->assertCanNotSeeTableRecords($archivedQnaAdvisors);
});

it('filter QnA Advisors with archived', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();
    
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any', 'qna_advisor.*.view']);

    actingAs($user);

    $qnaAdvisors = QnaAdvisor::factory()->count(2)->state([
        'archived_at' => null,
    ])->create();

    $archivedQnaAdvisors = QnaAdvisor::factory()->count(2)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListQnaAdvisors::class)
        ->assertCanSeeTableRecords($qnaAdvisors)
        ->assertCanNotSeeTableRecords($archivedQnaAdvisors)
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords($qnaAdvisors->merge($archivedQnaAdvisors));
});
