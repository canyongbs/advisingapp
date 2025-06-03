<?php

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

    $students->each(function (Student $student) use($tag) {
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

    $students->each(function (Student $student) use($tag) {
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