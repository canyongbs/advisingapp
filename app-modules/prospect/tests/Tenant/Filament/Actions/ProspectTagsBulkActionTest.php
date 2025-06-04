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

use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Models\Prospect;
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

    $prospects->each(function (Prospect $prospect) use ($tag) {
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

    $prospects->each(function (Prospect $prospect) use ($tag) {
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
