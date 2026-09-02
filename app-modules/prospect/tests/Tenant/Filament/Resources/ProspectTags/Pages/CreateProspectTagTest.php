<?php

use AdvisingApp\Prospect\Filament\Resources\ProspectTags\Pages\CreateProspectTag;
use App\Enums\TagType;
use App\Models\Tag;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateProspectTag does not allow for duplicate names of non-deleted prospect tags case insensitively', function () {
    asSuperAdmin();

    $tag = Tag::factory(['name' => 'Prospect Tag', 'type' => TagType::Prospect])->create();
    $tag->delete();

    livewire(CreateProspectTag::class)
        ->fillForm(['name' => 'prospect TAG'])
        ->call('create')
        ->assertHasNoFormErrors();

    livewire(CreateProspectTag::class)
        ->fillForm(['name' => 'prospect tag'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});