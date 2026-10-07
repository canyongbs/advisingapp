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

namespace AdvisingApp\Form\Filament\Forms\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

/**
 * Converts between the editor's `[{key, value}]` rows (key = option value,
 * value = label) and the stored options, which are either a `value => label`
 * map or, when `$asRows` is set, explicit `[{label, value}]` rows.
 *
 * Explicit rows are needed because PHP turns numeric keys such as "0" and "1"
 * into a list, which is indistinguishable from the `[{label, value}]` shape.
 */
class OptionsStateCast implements StateCast
{
    public function __construct(protected bool $asRows = false) {}

    /**
     * @return array<mixed, mixed>
     */
    public function get(mixed $state): array
    {
        $rows = $this->toArray($state);

        if ($rows === []) {
            return [];
        }

        if (! is_array($rows[array_key_first($rows)])) {
            return $this->asRows
                ? $this->toLabelValueRows($rows)
                : $rows;
        }

        if ($this->isLabelValueRows($rows)) {
            return $this->asRows
                ? $rows
                : array_column($rows, 'label', 'value');
        }

        $rows = array_filter($rows, fn (mixed $row): bool => is_array($row) && (string) ($row['key'] ?? '') !== '');

        if ($this->asRows) {
            return array_map(
                fn (array $row): array => ['label' => $row['value'] ?? '', 'value' => (string) $row['key']],
                array_values($rows),
            );
        }

        return array_column($rows, 'value', 'key');
    }

    /**
     * @return array<array{key: mixed, value: mixed}>
     */
    public function set(mixed $state): array
    {
        $rows = $this->toArray($state);

        if ($rows === []) {
            return [];
        }

        if (! is_array($rows[array_key_first($rows)])) {
            return array_map(
                fn (mixed $label, mixed $value): array => ['key' => $value, 'value' => $label],
                $rows,
                array_keys($rows),
            );
        }

        if ($this->isLabelValueRows($rows)) {
            return array_map(
                fn (array $row): array => ['key' => $row['value'], 'value' => $row['label']],
                array_values($rows),
            );
        }

        return $rows;
    }

    /**
     * @return array<mixed, mixed>
     */
    protected function toArray(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        if (! is_array($state)) {
            $state = json_decode($state, associative: true);
        }

        return is_array($state) ? $state : [];
    }

    /**
     * @param array<mixed, mixed> $rows
     */
    protected function isLabelValueRows(array $rows): bool
    {
        $first = $rows[array_key_first($rows)];

        return is_array($first) && array_key_exists('label', $first);
    }

    /**
     * @param array<mixed, mixed> $options
     *
     * @return array<array{label: mixed, value: string}>
     */
    protected function toLabelValueRows(array $options): array
    {
        return array_map(
            fn (mixed $label, mixed $value): array => ['label' => $label, 'value' => (string) $value],
            $options,
            array_keys($options),
        );
    }
}
