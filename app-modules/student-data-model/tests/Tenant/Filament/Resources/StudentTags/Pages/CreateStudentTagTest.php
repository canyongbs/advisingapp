<?php

use AdvisingApp\StudentDataModel\Filament\Resources\StudentTags\Pages\CreateStudentTag;
use App\Enums\TagType;
use App\Models\Tag;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateStudentTag does not allow for duplicate names of non-deleted student tags case insensitively', function () {
    asSuperAdmin();

    $tag = Tag::factory(['name' => 'Student Tag', 'type' => TagType::Student])->create();
    $tag->delete();

    livewire(CreateStudentTag::class)
        ->fillForm(['name' => 'student TAG'])
        ->call('create')
        ->assertHasNoFormErrors();

    livewire(CreateStudentTag::class)
        ->fillForm(['name' => 'student tag'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});