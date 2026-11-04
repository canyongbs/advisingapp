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

use AdvisingApp\Consent\Filament\Resources\ConsentAgreements\Pages\ListConsentAgreements;
use AdvisingApp\Consent\Models\ConsentAgreement;
use App\Features\ConsentAgreementTitleCitextFeature;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

it('allows updating a consent agreement with the same title as the current record', function (): void {
    asSuperAdmin();

    $consentAgreement = ConsentAgreement::factory()->create(['title' => 'AI Usage Agreement']);

    Livewire::test(ListConsentAgreements::class)
        ->callAction(TestAction::make('edit')->table($consentAgreement), data: [
            'title' => 'AI Usage Agreement',
        ])
        ->assertHasNoActionErrors();
});

it('does not allow updating a consent agreement with a title longer than 255 characters', function (): void {
    asSuperAdmin();

    $consentAgreement = ConsentAgreement::factory()->create(['title' => 'AI Usage Agreement']);

    Livewire::test(ListConsentAgreements::class)
        ->callAction(TestAction::make('edit')->table($consentAgreement), data: [
            'title' => str_repeat('a', 256),
        ])
        ->assertHasActionErrors(['title' => 'max']);

    expect($consentAgreement->refresh()->title)->toBe('AI Usage Agreement');
});

it('does not allow updating a consent agreement to a title matching another consent agreement case-insensitively', function (): void {
    asSuperAdmin();

    ConsentAgreement::factory()->create(['title' => 'AI Usage Agreement']);
    $consentAgreement = ConsentAgreement::factory()->create(['title' => 'Data Privacy Agreement']);

    Livewire::test(ListConsentAgreements::class)
        ->callAction(TestAction::make('edit')->table($consentAgreement), data: [
            'title' => 'ai usage agreement',
        ])
        ->assertHasActionErrors(['title' => 'unique']);

    expect($consentAgreement->refresh()->title)->toBe('Data Privacy Agreement');
});

it('allows updating a consent agreement to a title freed up by a soft-deleted consent agreement case-insensitively', function (): void {
    asSuperAdmin();

    $deletedConsentAgreement = ConsentAgreement::factory()->create(['title' => 'AI Usage Agreement']);
    DB::table('consent_agreements')->where('id', $deletedConsentAgreement->getKey())->update(['deleted_at' => now()]);

    $consentAgreement = ConsentAgreement::factory()->create(['title' => 'Data Privacy Agreement']);

    Livewire::test(ListConsentAgreements::class)
        ->callAction(TestAction::make('edit')->table($consentAgreement), data: [
            'title' => 'ai usage agreement',
        ])
        ->assertHasNoActionErrors();

    expect($consentAgreement->refresh()->title)->toBe('ai usage agreement');
});

it('rejects updating a consent agreement to a soft-deleted title before the citext migration activates', function (): void {
    ConsentAgreementTitleCitextFeature::deactivate();

    asSuperAdmin();

    $deletedConsentAgreement = ConsentAgreement::factory()->create(['title' => 'AI Usage Agreement']);
    DB::table('consent_agreements')->where('id', $deletedConsentAgreement->getKey())->update(['deleted_at' => now()]);

    $consentAgreement = ConsentAgreement::factory()->create(['title' => 'Data Privacy Agreement']);

    Livewire::test(ListConsentAgreements::class)
        ->callAction(TestAction::make('edit')->table($consentAgreement), data: [
            'title' => 'AI Usage Agreement',
        ])
        ->assertHasActionErrors(['title' => 'unique']);

    expect($consentAgreement->refresh()->title)->toBe('Data Privacy Agreement');
});
