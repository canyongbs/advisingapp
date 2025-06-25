<?php

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages\ViewQnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('View QnA Advisor is gated with proper access control', function () {
    
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisor,
            ])
        )->assertForbidden();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user)
        ->get(
            QnaAdvisorResource::getUrl('view', [
                'record' => $qnaAdvisor,
            ])
        )->assertSuccessful();
});

test('archive action visible when QnA Advisor is not archived', function () {

    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('archive')
        ->assertActionHidden('restore');
});

test('restore action visible when QnA Advisor is archived', function () {
    
    $settings = app(LicenseSettings::class);

    $settings->data->addons->qnaAdvisor = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::ConversationalAi)->create();

    $qnaAdvisor = QnaAdvisor::factory()->state([
        'archived_at' => now(),
    ])->create();

    $user->givePermissionTo('qna_advisor.view-any');
    $user->givePermissionTo('qna_advisor.*.view');

    actingAs($user);

    livewire(ViewQnaAdvisor::class, [
        'record' => $qnaAdvisor->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible('restore')
        ->assertActionHidden('archive');
});
