<?php

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ListQnAAdvisors;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('ListQnAAdvisors is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('index')
        )->assertSuccessful();
});

it('render QnA Advisors default to without archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any','qna_advisor.*.view']);

    actingAs($user);

    $qnaAdvisors = QnAAdvisor::factory()->count(3)->state([
        'archived_at' => null,
    ])->create();

    $archivedQnaAdvisors = QnAAdvisor::factory()->count(3)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListQnAAdvisors::class)
        ->assertCanSeeTableRecords($qnaAdvisors)
        ->assertCanNotSeeTableRecords($archivedQnaAdvisors);

});

it('filter QnA Advisors with archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $user->givePermissionTo(['qna_advisor.view-any','qna_advisor.*.view']);

    actingAs($user);

    $qnaAdvisors = QnAAdvisor::factory()->count(2)->state([
        'archived_at' => null,
    ])->create();

    $archivedQnaAdvisors = QnAAdvisor::factory()->count(2)->state([
        'archived_at' => now(),
    ])->create();

    livewire(ListQnAAdvisors::class)
        ->assertCanSeeTableRecords($qnaAdvisors)
        ->assertCanNotSeeTableRecords($archivedQnaAdvisors)
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords($qnaAdvisors->merge($archivedQnaAdvisors));

});