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

use AdvisingApp\Concern\Filament\Resources\ConcernStatuses\ConcernStatusResource;
use AdvisingApp\Concern\Filament\Resources\ConcernStatuses\Pages\EditConcernStatus;
use AdvisingApp\Concern\Models\ConcernStatus;
use App\Features\AlertStatusNameCitextFeature;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin();
});

it('can render the edit concern status page', function (): void {
    $concernStatus = ConcernStatus::factory()->create();

    get(ConcernStatusResource::getUrl('edit', ['record' => $concernStatus]))
        ->assertSuccessful();
});

it('can update a concern status', function (): void {
    $concernStatus = ConcernStatus::factory()->create(['name' => 'Under Review']);

    Livewire::test(EditConcernStatus::class, ['record' => $concernStatus->getRouteKey()])
        ->fillForm(['name' => 'In Progress'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($concernStatus->refresh()->name)->toBe('In Progress');
});

it('allows saving a concern status with a case change to its own name', function (): void {
    $concernStatus = ConcernStatus::factory()->create(['name' => 'Under Review']);

    Livewire::test(EditConcernStatus::class, ['record' => $concernStatus->getRouteKey()])
        ->fillForm(['name' => 'UNDER REVIEW'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($concernStatus->refresh()->name)->toBe('UNDER REVIEW');
});

it('does not allow updating a concern status to a name matching another non-deleted concern status case-insensitively', function (): void {
    ConcernStatus::factory()->create(['name' => 'Under Review']);
    $concernStatus = ConcernStatus::factory()->create(['name' => 'In Progress']);

    Livewire::test(EditConcernStatus::class, ['record' => $concernStatus->getRouteKey()])
        ->fillForm(['name' => 'under review'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($concernStatus->refresh()->name)->toBe('In Progress');
});

it('allows updating a concern status to a name freed up by a soft-deleted concern status case-insensitively', function (): void {
    $deletedConcernStatus = ConcernStatus::factory()->create(['name' => 'Under Review']);
    $deletedConcernStatus->delete();

    $concernStatus = ConcernStatus::factory()->create(['name' => 'In Progress']);

    Livewire::test(EditConcernStatus::class, ['record' => $concernStatus->getRouteKey()])
        ->fillForm(['name' => 'under review'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($concernStatus->refresh()->name)->toBe('under review');
});

it('rejects updating a concern status to a soft-deleted name before the citext migration activates', function (): void {
    AlertStatusNameCitextFeature::deactivate();

    $deletedConcernStatus = ConcernStatus::factory()->create(['name' => 'Under Review']);
    $deletedConcernStatus->delete();

    $concernStatus = ConcernStatus::factory()->create(['name' => 'In Progress']);

    Livewire::test(EditConcernStatus::class, ['record' => $concernStatus->getRouteKey()])
        ->fillForm(['name' => 'Under Review'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($concernStatus->refresh()->name)->toBe('In Progress');
});
