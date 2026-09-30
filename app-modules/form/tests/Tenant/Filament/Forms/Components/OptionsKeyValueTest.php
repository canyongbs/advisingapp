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

    /**
     * @param array<string, string> $options
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
        $this->getSchema('form')?->getState();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

it('hydrates stored value => label options as label => value rows', function () {
    $host = livewire(OptionsKeyValueTestHost::class, ['options' => ['us' => 'United States', 'ca' => 'Canada']]);

    expect($host->instance()->form->getRawState()['options'])->toBe([
        ['key' => 'United States', 'value' => 'us'],
        ['key' => 'Canada', 'value' => 'ca'],
    ]);
});

it('derives a value from the label for new options, keeping existing values', function () {
    $host = livewire(OptionsKeyValueTestHost::class, ['options' => ['us' => 'United States']])
        ->set('data.options', [
            ['key' => 'United States', 'value' => 'us'],
            ['key' => 'New Option', 'value' => ''],
        ]);

    expect($host->instance()->form->getState()['options'])->toBe([
        'us' => 'United States',
        'new-option' => 'New Option',
    ]);
});

it('re-derives the value of a renamed option, but keeps the values of unchanged options', function () {
    $host = livewire(OptionsKeyValueTestHost::class, ['options' => ['us' => 'United States', 'ca' => 'Canada']]);

    $host->set('data.options', [
        ['key' => 'United States', 'value' => 'us'],
        ['key' => 'Canada Renamed', 'value' => 'ca'],
    ]);

    expect($host->instance()->form->getRawState()['options'])->toBe([
        ['key' => 'United States', 'value' => 'us'],
        ['key' => 'Canada Renamed', 'value' => 'canada-renamed'],
    ]);
});

it('validates that every label generates a distinct, non-empty value', function (array $rows) {
    livewire(OptionsKeyValueTestHost::class)
        ->set('data.options', $rows)
        ->call('save')
        ->assertHasErrors(['data.options']);
})->with([
    'punctuation-only label' => [[['key' => '!!!', 'value' => '']]],
    'labels that slug to the same value' => [[['key' => 'A B', 'value' => ''], ['key' => 'A-B', 'value' => '']]],
]);
