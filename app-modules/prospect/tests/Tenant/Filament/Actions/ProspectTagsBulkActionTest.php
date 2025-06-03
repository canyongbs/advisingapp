<?php

use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\TagType;
use App\Models\Tag;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can bulk assign tags to prospects without remove the prior tags', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    $user->givePermissionTo('prospect.*.update');

    actingAs($user);

    $tag = Tag::factory()->state(['type' => TagType::Prospect])->create();

    $prospects = Prospect::factory()->hasAttached($tag)->count(5)->create();

    $prospects->each(function (Prospect $prospect) use($tag) {
        expect($prospect->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
    });

    $newTag = Tag::factory()->state(['type' => TagType::Prospect])->create();

    livewire(ListProspects::class)
        ->callTableBulkAction('bulkProspectTags', $prospects, [
            'tag_ids' => [$newTag->getKey()],
            'remove_prior' => false,
        ])
        ->assertSuccessful();

    $prospects->each(function (Prospect $prospect) use ($tag, $newTag) {
        expect($prospect->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
        expect($prospect->tags()->where('tag_id', $newTag->getKey())->exists())->toBeTrue();
    });
});

it('can bulk assign tags to prospects and remove the prior tags', function () {
   $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');
    $user->givePermissionTo('prospect.*.update');

    actingAs($user);

    $tag = Tag::factory()->state(['type' => TagType::Prospect])->create();

    $prospects = Prospect::factory()->hasAttached($tag)->count(5)->create();

    $prospects->each(function (Prospect $prospect) use($tag) {
        expect($prospect->tags()->where('tag_id', $tag->getKey())->exists())->toBeTrue();
    });

    $newTag = Tag::factory()->state(['type' => TagType::Prospect])->create();

    livewire(ListProspects::class)
        ->callTableBulkAction('bulkProspectTags', $prospects, [
            'tag_ids' => [$newTag->getKey()],
            'remove_prior' => true,
        ])
        ->assertSuccessful();

    $prospects->each(function (Prospect $prospect) use ($tag, $newTag) {
        expect($prospect->tags()->where('tag_id', $tag->getKey())->exists())->toBeFalse();
        expect($prospect->tags()->where('tag_id', $newTag->getKey())->exists())->toBeTrue();
    });
});