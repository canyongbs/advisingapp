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

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

$mountComponent = function (TextEntry | TextColumn | ColorColumn | IconColumn $component): TextEntry | TextColumn | ColorColumn | IconColumn {
    $livewire = new class () extends Component implements HasSchemas, HasTable {
        use InteractsWithSchemas;
        use InteractsWithTable;
    };

    if ($component instanceof TextEntry) {
        return $component->container(Schema::make($livewire)->record(new User()));
    }

    return $component->table(Table::make($livewire))->record(new User());
};

$components = [
    'text entry' => fn () => $mountComponent(TextEntry::make('value')),
    'text column' => fn () => $mountComponent(TextColumn::make('value')),
    'color column' => fn () => $mountComponent(ColorColumn::make('value')),
    'icon column' => fn () => $mountComponent(IconColumn::make('value')),
];

it('renders N/A for missing display values', function (TextEntry | TextColumn | ColorColumn | IconColumn $component, mixed $state) {
    $component->state($state);

    expect($component->toEmbeddedHtml())->toContain('N/A');
})->with($components)->with([
    'null' => [null],
    'empty string' => [''],
    'whitespace' => ['   '],
    'empty list' => [[]],
    'empty collection' => fn () => [collect()],
]);

it('allows an explicit placeholder override', function (TextEntry | TextColumn | ColorColumn | IconColumn $component) {
    $component->state(null)->placeholder('Unavailable');

    expect($component->toEmbeddedHtml())->toContain('Unavailable')->not->toContain('N/A');
})->with($components);

it('allows the placeholder to be disabled', function (TextEntry | TextColumn | ColorColumn | IconColumn $component) {
    $component->state(null)->placeholder(null);

    expect($component->toEmbeddedHtml())->not->toContain('N/A');
})->with($components);

it('preserves legitimate falsy values', function (TextEntry | TextColumn | ColorColumn | IconColumn $component, mixed $state) {
    $component->state($state);

    expect($component->getState())->toBe($state)
        ->and($component->toEmbeddedHtml())->not->toContain('N/A');
})->with($components)->with([
    'false' => [false],
    'zero' => [0],
    'string zero' => ['0'],
]);

it('preserves non-missing values and their presentation', function (TextEntry | TextColumn | ColorColumn | IconColumn $component, mixed $state, string $expected) use ($mountComponent) {
    $component = $mountComponent($component);
    $component->state($state);

    expect($component->toEmbeddedHtml())->toContain($expected)->not->toContain('N/A');
})->with([
    'text entry' => fn () => [TextEntry::make('value'), 'Available', 'Available'],
    'text column' => fn () => [TextColumn::make('value'), 'Available', 'Available'],
    'color column' => fn () => [ColorColumn::make('value'), '#ff0000', '#ff0000'],
    'false boolean icon' => fn () => [IconColumn::make('value')->boolean(), false, '<svg'],
    'true boolean icon' => fn () => [IconColumn::make('value')->boolean(), true, '<svg'],
    'formatted text' => fn () => [TextColumn::make('value')->formatStateUsing(fn (string $state): string => "Formatted {$state}"), 'Available', 'Formatted Available'],
    'explicit default' => fn () => [TextColumn::make('value')->default('Default value'), null, 'Default value'],
]);
