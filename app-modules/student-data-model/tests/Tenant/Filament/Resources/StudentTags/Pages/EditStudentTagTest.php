<?php

use AdvisingApp\StudentDataModel\Filament\Resources\StudentTags\Pages\EditStudentTag;
use App\Enums\TagType;
use App\Models\Tag;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditStudentTag does not allow for duplicate names of non-deleted student tags case insensitively', function () {
    asSuperAdmin();

    $deletedTag = Tag::factory(['name' => 'Student Tag', 'type' => TagType::Student])->create();
    $systemUser = Tag::factory(['name' => 'Test Student Tag',  'type' => TagType::Student])->create();
    Tag::factory(['name' => 'Other Student Tag',  'type' => TagType::Student])->create();

    $deletedTag->delete();

    livewire(EditStudentTag::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'student tag'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditStudentTag::class, ['record' => $systemUser->getRouteKey()])
        ->fillForm(['name' => 'OTHER Student tag'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});