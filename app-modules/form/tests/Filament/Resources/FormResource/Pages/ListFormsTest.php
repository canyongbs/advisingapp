<?php

use function Tests\asSuperAdmin;

use AdvisingApp\Form\Models\Form;

use function Pest\Livewire\livewire;

use AdvisingApp\Form\Filament\Resources\FormResource\Pages\ListForms;

it('can duplicate a form and all of its content', function () {
    asSuperAdmin();

    // Given that we have a form
    $form = Form::factory()->create();

    expect(Form::count())->toBe(1);

    // And we duplicate it
    livewire(ListForms::class)
        ->assertStatus(200)
        ->callTableAction('Duplicate', $form);

    // The form, along with all of its content, should be duplicated
    expect(Form::count())->toBe(2);
    expect(Form::where('id', '<>', $form->id)->first()->name)->toBe("Copy - {$form->name}");
    expect(Form::where('id', '<>', $form->id)->first()->fields->count())->toBe($form->fields->count());
    expect(Form::where('id', '<>', $form->id)->first()->steps->count())->toBe($form->steps->count());
});
