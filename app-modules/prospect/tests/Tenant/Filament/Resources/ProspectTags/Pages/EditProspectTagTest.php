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

use AdvisingApp\Prospect\Filament\Resources\ProspectTags\Pages\EditProspectTag;
use App\Enums\TagType;
use App\Models\Tag;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditProspectTag does not allow for duplicate names of non-deleted prospect tags case insensitively', function () {
    asSuperAdmin();

    $deletedTag = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
    $tag = Tag::factory(['name' => 'Test Prospect Tag',  'type' => TagType::Prospect])->create();
    Tag::factory(['name' => 'Other Prospect Tag',  'type' => TagType::Prospect])->create();

    $deletedTag->delete();

    livewire(EditProspectTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'prospect tag'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditProspectTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'OTHER Prospect tag'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('EditProspectTag does allow for non-duplicate names of non-deleted prospect tags', function () {
    asSuperAdmin();

    $tag = Tag::factory(['name' => 'Prospect Tag 1', 'type' => TagType::Prospect])->create();
    $deletedTag = Tag::factory(['name' => 'Prospect Tag 2', 'type' => TagType::Prospect])->create();
    $deletedTag->delete();

    livewire(EditProspectTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'Prospect Tag 2'])
        ->call('save')
        ->assertHasNoFormErrors();
});

test('EditProspectTag does allow for duplicate names of student tags', function () {
    asSuperAdmin();

    Tag::factory(['name' => 'Tag', 'type' => TagType::Student])->create();
    $tag = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();

    livewire(EditProspectTag::class, ['record' => $tag->getRouteKey()])
        ->fillForm(['name' => 'Tag'])
        ->call('save')
        ->assertHasNoFormErrors();
});
