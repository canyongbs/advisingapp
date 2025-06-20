<?php

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages\ViewQnAAdvisor;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('ViewQnAAdvisor is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnAAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisors,
            ])
        )->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user)
        ->get(
            QnAAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisors,
            ])
        )->assertSuccessful();
});

test('archive action visible when QnA Advisor is not archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnAAdvisor::factory()->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnAAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('archive')
        ->assertActionHidden('restore');
});

test('restore action visible when QnA Advisor is archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnAAdvisor::factory()->state([
        'archived_at' => now(),
    ])->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnAAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');
});
