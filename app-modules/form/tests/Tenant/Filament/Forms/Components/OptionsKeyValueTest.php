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

use AdvisingApp\Form\Filament\Forms\Components\OptionsKeyValue;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

use function Pest\Livewire\livewire;

class OptionsKeyValueTestHost extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var array<mixed, mixed> */
    public array $savedOptions = [];

    /**
        * @param array<mixed, mixed> $options
     */
    public function mount(array $options = []): void
    {
        $this->getSchema('form')?->fill(['options' => $options]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([OptionsKeyValue::make('options')])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->getSchema('form')?->getState();

        $this->savedOptions = $state['options'] ?? [];
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

it('hydrates options saved as a list of label and value rows', function () {
    $host = livewire(OptionsKeyValueTestHost::class, ['options' => [
        ['label' => 'United States', 'value' => 'us'],
        ['label' => 'Canada', 'value' => 'ca'],
    ]]);

    expect($host->instance()->form->getRawState()['options'])->toBe([
        ['key' => 'us', 'value' => 'United States'],
        ['key' => 'ca', 'value' => 'Canada'],
    ]);
});

it('saves legacy options as label and value rows without changing their values', function (array $options, array $expectedOptions) {
    livewire(OptionsKeyValueTestHost::class, ['options' => $options])
        ->assertSet('savedOptions', [])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('savedOptions', $expectedOptions);
})->with([
    'string values' => [
        ['us' => 'United States', 'ca' => 'Canada'],
        [
            ['label' => 'United States', 'value' => 'us'],
            ['label' => 'Canada', 'value' => 'ca'],
        ],
    ],
    'numeric values including zero' => [
        ['0%', '1%'],
        [
            ['label' => '0%', 'value' => '0'],
            ['label' => '1%', 'value' => '1'],
        ],
    ],
    'custom values unrelated to their labels' => [
        ['LEGACY-ID-7' => 'Career planning', '01' => 'Academic advising'],
        [
            ['label' => 'Career planning', 'value' => 'LEGACY-ID-7'],
            ['label' => 'Academic advising', 'value' => '01'],
        ],
    ],
]);

it('renders the label column before the generated value column', function () {
    livewire(OptionsKeyValueTestHost::class)
        ->assertSeeHtmlInOrder(['aria-label="Label"', 'aria-label="Value"']);
});

it('validates that every label generates a distinct value', function () {
    livewire(OptionsKeyValueTestHost::class)
        ->set('data.options', [['key' => 'a-b', 'value' => 'A B'], ['key' => 'a-b', 'value' => 'A-B']])
        ->call('save')
        ->assertHasErrors(['data.options']);
});

it('rejects incomplete options without discarding their editor state', function (array $options) {
    livewire(OptionsKeyValueTestHost::class)
        ->set('data.options', $options)
        ->call('save')
        ->assertHasErrors(['data.options'])
        ->assertSee('Each option must have a label and a value.')
        ->assertSet('data.options', $options);
})->with([
    'label required' => [[['key' => 'us', 'value' => '']]],
    'label cannot be whitespace' => [[['key' => 'us', 'value' => '   ']]],
    'value required' => [[['key' => '', 'value' => 'United States']]],
]);

it('saves zero as an option label and value', function () {
    livewire(OptionsKeyValueTestHost::class)
        ->set('data.options', [['key' => '0', 'value' => '0']])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('savedOptions', [['label' => '0', 'value' => '0']]);
});
