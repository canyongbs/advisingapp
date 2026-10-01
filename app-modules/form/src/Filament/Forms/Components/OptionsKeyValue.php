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

namespace AdvisingApp\Form\Filament\Forms\Components;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlock;
use Closure;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\StateCasts\KeyValueStateCast;

/**
 * A "Label" / "Value" options editor for choice-style form fields (select,
 * radio, checkboxes).
 *
 * The state is a standard KeyValue state (option value => label), but the
 * label column is rendered first and the value is generated from the label
 * in the browser, so no network requests are needed to add or edit rows.
 */
class OptionsKeyValue extends KeyValue
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->keyLabel('Value')
            ->valueLabel('Label')
            ->reorderable()
            ->afterStateHydrated(function (OptionsKeyValue $component): void {
                $state = $component->getRawState();

                if (is_array($state) && is_array($state[0] ?? null) && array_key_exists('label', $state[0])) {
                    $component->state(collect($state)->pluck('label', 'value')->all());
                }
            })
            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                $rows = collect(app(KeyValueStateCast::class)->set($value))
                    ->filter(fn (array $row): bool => filled($row['value'] ?? null));

                FormFieldBlock::validateOptionValues($rows->pluck('key')->all(), $fail);
            });
    }

    public function toEmbeddedHtml(): string
    {
        $slotHtml = view('form::components.options-key-value', [
            'addActionHtml' => $this->getAction('add')->toHtml(),
            'alpineAttributes' => $this->getExtraAlpineAttributeBag()->class(['fi-fo-key-value-table-ctn'])->toHtml(),
            'debounce' => $this->getLiveDebounce() ?? '500ms',
            'deleteActionHtml' => $this->getAction('delete')->toHtml(),
            'entangleExpression' => $this->applyStateBindingModifiers("\$entangle('{$this->getStatePath()}')"),
            'id' => $this->getId(),
            'isAddable' => $this->isAddable(),
            'isDeletable' => $this->isDeletable(),
            'isDisabled' => $this->isDisabled(),
            'isReorderable' => $this->isReorderable(),
            'keyLabel' => $this->getKeyLabel(),
            'livewireKey' => $this->getLivewireKey(),
            'reorderActionHtml' => $this->getAction('reorder')->toHtml(),
            'reorderAnimationDuration' => $this->getReorderAnimationDuration(),
            'valueLabel' => $this->getValueLabel(),
        ])->render();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $this->getExtraAttributeBag()->class(['fi-fo-key-value']),
            ),
            extraWrapperAttributes: ['class' => 'fi-fo-key-value-wrp'],
            labelTag: 'div',
        );
    }
}
