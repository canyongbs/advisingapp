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

use AdvisingApp\StudentDataModel\Models\Term;
use App\Models\User;

it('allows a manually maintained term to be created, updated and deleted with the settings permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.create', 'settings.*.update', 'settings.*.delete']);

    $term = Term::factory()->withoutSisTermId()->create();

    expect($user->can('create', Term::class))->toBeTrue()
        ->and($user->can('update', $term))->toBeTrue()
        ->and($user->can('delete', $term))->toBeTrue();
});

it('does not allow a term to be created while terms are synced from the SIS', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.create');

    Term::factory()->create();

    expect($user->can('create', Term::class))->toBeFalse();
});

it('allows a term to be created when the only terms synced from the SIS are deleted', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.create');

    Term::factory()->create(['deleted_at' => now()]);

    expect($user->can('create', Term::class))->toBeTrue();
});

it('does not allow a term synced from the SIS to be updated or deleted', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.*.update', 'settings.*.delete']);

    $term = Term::factory()->create();

    expect($user->can('update', $term))->toBeFalse()
        ->and($user->can('delete', $term))->toBeFalse();
});
