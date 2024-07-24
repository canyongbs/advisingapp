<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Livewire\Livewire;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\TextInput;
use FilamentTiptapEditor\Tests\TestCase;
use FilamentTiptapEditor\Tests\Models\Page;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\Tests\Fixtures\Livewire as LivewireFixture;
use FilamentTiptapEditor\Tests\Resources\PageResource\Pages\EditPage;
use FilamentTiptapEditor\Tests\Resources\PageResource\Pages\CreatePage;

/** @var TestCase $this */
it('has editor field', function () {
    Livewire::test(TestComponentWithForm::class)
        ->assertFormFieldExists('html_content')
        ->assertFormFieldExists('json_content')
        ->assertFormFieldExists('text_content');
});

it('creates record', function () {
    $page = Page::factory()->make();

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => $page->title,
            'html_content' => $page->html_content,
            'json_content' => $page->json_content,
            'text_content' => $page->text_content,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Page::class, [
        'title' => $page->title,
    ]);

    $storedPage = Page::query()->where('title', $page->title)->first();

    expect($storedPage)
        ->html_content->toBe($page->html_content)
        ->json_content->toBe($page->json_content);
});

it('updates record', function () {
    $page = Page::factory()->create();
    $newData = Page::factory()->make();

    Livewire::test(EditPage::class, [
        'record' => $page->getRouteKey(),
    ])
        ->fillForm([
            'title' => $newData->title,
            'html_content' => $newData->html_content,
            'json_content' => $newData->json_content,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Page::class, [
        'title' => $newData->title,
    ]);

    $storedPage = Page::query()->where('id', $page->id)->first();

    expect($storedPage)
        ->html_content->toBe($newData->html_content)
        ->json_content->toBe($newData->json_content);
});

it('can create null record', function () {
    $page = Page::factory()->make();

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => $page->title,
            'html_content' => null,
            'json_content' => null,
            'text_content' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Page::class, [
        'title' => $page->title,
    ]);

    $storedPage = Page::query()->where('title', $page->title)->first();

    expect($storedPage)
        ->html_content->toBeNull()
        ->json_content->toBeNull();
});

class TestComponentWithForm extends LivewireFixture
{
    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->model(Page::class)
            ->schema([
                TextInput::make('title'),
                TiptapEditor::make('html_content')
                    ->output(TiptapOutput::Html),
                TiptapEditor::make('json_content')
                    ->output(TiptapOutput::Json),
                TiptapEditor::make('text_content')
                    ->output(TiptapOutput::Text),
            ]);
    }

    public function render(): View
    {
        return view('fixtures.form');
    }
}
