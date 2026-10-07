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

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('denies viewing term attributes without a Retention CRM license', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['enrollment.view-any', 'enrollment.*.view']);

    actingAs($user);

    expect(Gate::allows('viewAny', StudentTermAttribute::class))->toBeFalse()
        ->and(Gate::allows('view', StudentTermAttribute::factory()->create()))->toBeFalse();
});

it('does not allow changing term attributes, even for a super admin', function (string $ability) {
    asSuperAdmin();

    $termAttribute = StudentTermAttribute::factory()->create();

    expect(Gate::allows($ability, $ability === 'create' ? StudentTermAttribute::class : $termAttribute))->toBeFalse();
})->with(['create', 'update', 'delete', 'restore', 'forceDelete']);

describe('authorization', function () {
    it('denies viewing term attributes without the `enrollment.view-any` permission', function () {
        actingAs(User::factory()->licensed(Student::getLicenseType())->create());

        expect(Gate::allows('viewAny', StudentTermAttribute::class))->toBeFalse();
    });

    it('allows viewing term attributes with the `enrollment.view-any` permission', function () {
        $user = User::factory()->licensed(Student::getLicenseType())->create();
        $user->givePermissionTo('enrollment.view-any');

        actingAs($user);

        expect(Gate::allows('viewAny', StudentTermAttribute::class))->toBeTrue();
    });

    it('denies viewing a term attribute without the `enrollment.*.view` permission', function () {
        actingAs(User::factory()->licensed(Student::getLicenseType())->create());

        expect(Gate::allows('view', StudentTermAttribute::factory()->create()))->toBeFalse();
    });

    it('allows viewing a term attribute with the `enrollment.*.view` permission', function () {
        $user = User::factory()->licensed(Student::getLicenseType())->create();
        $user->givePermissionTo('enrollment.*.view');

        actingAs($user);

        expect(Gate::allows('view', StudentTermAttribute::factory()->create()))->toBeTrue();
    });
});
