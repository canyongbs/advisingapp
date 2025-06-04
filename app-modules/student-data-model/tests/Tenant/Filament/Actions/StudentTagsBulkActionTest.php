<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can bulk assign tags to students without remove the prior tags', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    $user->givePermissionTo('student.*.update');

    actingAs($user);

    $tag = Tag::factory()->state(['type' => TagType::Student])->create();

    $students = Student::factory()->hasAttached($tag)->count(5)->create();

    $students->each(function (Student $student) use ($tag) {
        expect($student->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
    });

    $newTag = Tag::factory()->state(['type' => TagType::Student])->create();

    livewire(ListStudents::class)
        ->callTableBulkAction('bulkStudentTags', $students, [
            'tag_ids' => [$newTag->getKey()],
            'remove_prior' => false,
        ])
        ->assertSuccessful();

    $students->each(function (Student $student) use ($tag, $newTag) {
        expect($student->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
        expect($student->tags()->where('tag_id', $newTag->getKey())->exists())->toBeTrue();
    });
});

it('can bulk assign tags to students and remove the prior tags', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    $user->givePermissionTo('student.*.update');

    actingAs($user);

    $tag = Tag::factory()->state(['type' => TagType::Student])->create();

    $students = Student::factory()->hasAttached($tag)->count(5)->create();

    $students->each(function (Student $student) use ($tag) {
        expect($student->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
    });

    $newTag = Tag::factory()->state(['type' => TagType::Student])->create();

    livewire(ListStudents::class)
        ->callTableBulkAction('bulkStudentTags', $students, [
            'tag_ids' => [$newTag->getKey()],
            'remove_prior' => true,
        ])
        ->assertSuccessful();

    $students->each(function (Student $student) use ($tag, $newTag) {
        expect($student->tags()->where('tag_id', $tag->getKey())->exists())->toBeFalse();
        expect($student->tags()->where('tag_id', $newTag->getKey())->exists())->toBeTrue();
    });
});
