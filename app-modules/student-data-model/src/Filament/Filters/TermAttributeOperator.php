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

namespace AdvisingApp\StudentDataModel\Filament\Filters;

use AdvisingApp\StudentDataModel\Enums\StudentTermAttributeField;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use Filament\Forms\Components\Select;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TermAttributeOperator extends Operator
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('termAttribute');

        $this->label(fn (): string => $this->isInverse() ? 'Is not' : 'Is');

        $this->summary(function (): string {
            if (blank($this->getSettings())) {
                return '';
            }

            $termLabel = Term::query()
                ->where('sis_term_id', $this->getStringSetting('sis_term_id'))
                ->first()
                ?->getDisplayName() ?? 'Unknown Term';

            $attributeLabel = $this->resolveAttribute($this->getSettings()['attribute'] ?? null)?->getLabel() ?? 'Unknown Attribute';

            $operatorLabel = $this->isInverse() ? 'is not' : 'is';

            return "{$termLabel}: {$attributeLabel} {$operatorLabel} \"{$this->getStringSetting('value')}\"";
        });
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('sis_term_id')
                ->label('Term')
                ->options(fn (): array => Term::query()
                    ->whereIn('sis_term_id', StudentTermAttribute::query()->select('sis_term_id'))
                    ->orderByRaw('start_date DESC NULLS LAST')
                    ->get(['sis_term_id', 'name', 'start_date'])
                    ->mapWithKeys(fn (Term $term): array => [$term->sis_term_id => $term->getDisplayName()])
                    ->all())
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('value', null)),
            Select::make('attribute')
                ->label('Attribute')
                ->options(StudentTermAttributeField::class)
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('value', null)),
            Select::make('value')
                ->label('Value')
                ->options(fn (Get $get): array => $this->getValueOptions($get('sis_term_id'), $get('attribute')))
                ->searchable()
                ->required(),
        ];
    }

    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function applyToBaseQuery(Builder $query): Builder
    {
        $termId = $this->getStringSetting('sis_term_id');
        $attribute = $this->getSettings()['attribute'] ?? null;
        $value = $this->getStringSetting('value');

        if (blank($termId) || blank($attribute) || blank($value)) {
            return $query;
        }

        $field = $this->resolveAttribute($attribute);

        // Only allow-listed columns may reach the query, so an unknown attribute matches no students.
        if (! $field) {
            return $query->whereRaw('1 = 0');
        }

        return $query->{$this->isInverse() ? 'whereDoesntHave' : 'whereHas'}(
            'termAttributes',
            fn (Builder $query) => $query
                ->where('sis_term_id', $termId)
                ->where($field->value, $value),
        );
    }

    /**
     * The distinct existing values of the attribute for the selected term.
     *
     * @return array<int|string, string>
     */
    protected function getValueOptions(mixed $termId, mixed $attribute): array
    {
        $field = $this->resolveAttribute($attribute);

        if (blank($termId) || ! is_scalar($termId) || ! $field) {
            return [];
        }

        return StudentTermAttribute::query()
            ->where('sis_term_id', (string) $termId)
            ->whereNotNull($field->value)
            ->distinct()
            ->orderBy($field->value)
            ->pluck($field->value)
            ->mapWithKeys(fn (string $value): array => [$value => $value])
            ->all();
    }

    /**
     * The attribute setting is an enum instance when read from the live table filter form
     * (the select casts it), but a plain string when read from a group's saved filters.
     */
    protected function resolveAttribute(mixed $attribute): ?StudentTermAttributeField
    {
        if ($attribute instanceof StudentTermAttributeField) {
            return $attribute;
        }

        return is_string($attribute) ? StudentTermAttributeField::tryFrom($attribute) : null;
    }
}
