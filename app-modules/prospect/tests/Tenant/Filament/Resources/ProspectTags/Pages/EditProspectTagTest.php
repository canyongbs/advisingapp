<?php

use AdvisingApp\Prospect\Filament\Resources\ProspectTags\Pages\EditProspectTag;
use App\Enums\TagType;
use App\Models\Tag;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditProspectTag does not allow for duplicate names of non-deleted prospect tags case insensitively', function () {
    asSuperAdmin();

    $deletedTag = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
    $systemUser = Tag::factory(['name' => 'Test Prospect Tag',  'type' => TagType::Prospect])->create();
    Tag::factory(['name' => 'Other Prospect Tag',  'type' => TagType::Prospect])->create();

    $deletedTag->delete();

    livewire(EditProspectTag::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'prospect tag'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditProspectTag::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'OTHER Prospect tag'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});