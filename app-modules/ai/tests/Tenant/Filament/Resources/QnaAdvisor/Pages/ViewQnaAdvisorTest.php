<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ViewQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('ViewQnaAdvisor is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisors,
            ])
        )->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisors,
            ])
        )->assertSuccessful();
});

test('archive action visible when QnA Advisor is not archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnaAdvisor::factory()->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnaAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('archive')
        ->assertActionHidden('restore');
});

test('restore action visible when QnA Advisor is archived', function () {
    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisors = QnaAdvisor::factory()->state([
        'archived_at' => now(),
    ])->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnaAdvisor::class, [
        'record' => $qnaAdvisors->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');
});
