<?php

use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can bulk subscribe students without remove the prior subscriptions',function(){
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $anotherUser = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    $user->givePermissionTo('student.*.update');

    actingAs($user);

    $students = Student::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(5)->create();

    $students->each(function (Student $student) use ($user) {
        expect($student->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });

    livewire(ListStudents::class)
        ->callTableBulkAction('bulkSubscription', $students, [
            'user_ids' => [$anotherUser->getKey()],
            'remove_prior' => false,
        ])
        ->assertSuccessful();

    $students->each(function (Student $student) use ($user,$anotherUser) {
        expect($student->subscriptions()->where('user_id', $anotherUser->getKey())->exists())->toBeTrue();
        expect($student->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });
})->only();

it('can bulk subscribe students and remove the prior subscriptions',function(){
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $anotherUser = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');
    $user->givePermissionTo('student.*.update');

    actingAs($user);

    $students = Student::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(5)->create();

    $students->each(function (Student $student) use ($user) {
        expect($student->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });

    livewire(ListStudents::class)
        ->callTableBulkAction('bulkSubscription', $students, [
            'user_ids' => [$anotherUser->getKey()],
            'remove_prior' => true,
        ])
        ->assertSuccessful();

    $students->each(function (Student $student) use ($user,$anotherUser) {
        expect($student->subscriptions()->where('user_id', $anotherUser->getKey())->exists())->toBeTrue();
        expect($student->subscriptions()->where('user_id', $user->getKey())->exists())->toBeFalse();
    });
});

it('can bulk subscribe prospects without remove the prior subscriptions',function(){
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $anotherUser = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    $user->givePermissionTo('prospect.*.update');

    actingAs($user);

    $prospects = Prospect::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(5)->create();

    $prospects->each(function (Prospect $prospect) use ($user) {
        expect($prospect->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });

    livewire(ListProspects::class)
        ->callTableBulkAction('bulkSubscription', $prospects, [
            'user_ids' => [$anotherUser->getKey()],
            'remove_prior' => false,
        ])
        ->assertSuccessful();

    $prospects->each(function (Prospect $prospect) use ($user,$anotherUser) {
        expect($prospect->subscriptions()->where('user_id', $anotherUser->getKey())->exists())->toBeTrue();
        expect($prospect->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });
});

it('can bulk subscribe prospects and remove the prior subscriptions',function(){
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();
    $anotherUser = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    $user->givePermissionTo('prospect.*.update');

    actingAs($user);

    $prospects = Prospect::factory()->has(
        Subscription::factory()->state([
            'user_id' => $user->getKey(),
        ]),
        'subscriptions'
    )->count(5)->create();

    $prospects->each(function (Prospect $prospect) use ($user) {
        expect($prospect->subscriptions()->where('user_id', $user->getKey())->exists())->toBeTrue();
    });

    livewire(ListProspects::class)
        ->callTableBulkAction('bulkSubscription', $prospects, [
            'user_ids' => [$anotherUser->getKey()],
            'remove_prior' => true,
        ])
        ->assertSuccessful();

    $prospects->each(function (Prospect $prospect) use ($user,$anotherUser) {
        expect($prospect->subscriptions()->where('user_id', $anotherUser->getKey())->exists())->toBeTrue();
        expect($prospect->subscriptions()->where('user_id', $user->getKey())->exists())->toBeFalse();
    });
});