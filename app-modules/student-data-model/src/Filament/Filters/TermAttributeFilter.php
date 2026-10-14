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
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * The "Term Attribute" filter on the students list. Its form, query and summary are shared with
 * {@see TermAttributeOperator}, which offers the same filter in groups.
 */
class TermAttributeFilter
{
    public static function make(): Filter
    {
        return Filter::make('termAttribute')
            ->label('Term Attribute')
            ->schema(static::getFormSchema(isRequired: false))
            ->columnSpanFull()
            ->query(fn (Builder $query, array $data): Builder => static::applyToQuery(
                $query,
                $data['sis_term_id'] ?? null,
                $data['attribute'] ?? null,
                $data['value'] ?? null,
            ))
            ->indicateUsing(fn (array $data): ?string => blank($data['sis_term_id'] ?? null) || blank($data['attribute'] ?? null) || blank($data['value'] ?? null)
                ? null
                : static::getSummary($data['sis_term_id'], $data['attribute'], $data['value']))
            ->visible(fn (): bool => app(StudentInformationSystemSettings::class)->hasTermAttributes());
    }

    /**
     * The fields are completed in order (term, then attribute, then value), so each one is disabled until the previous ones are selected.
     * Group rules require every field so an incomplete rule cannot be saved, while the students list
     * filter is optional and simply does not apply until all three fields are selected.
     *
     * @return array<Component>
     */
    public static function getFormSchema(bool $isRequired = true): array
    {
        return [
            Select::make('sis_term_id')
                ->label('Term')
                ->options(fn (): array => static::getTermOptions())
                ->searchable()
                ->required($isRequired)
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('value', null)),
            Select::make('attribute')
                ->label('Attribute')
                ->options(StudentTermAttributeField::class)
                ->searchable()
                ->required($isRequired)
                ->disabled(fn (Get $get): bool => blank($get('sis_term_id')))
                ->placeholder(fn (Get $get): ?string => blank($get('sis_term_id')) ? 'Select a term first' : null)
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('value', null)),
            Select::make('value')
                ->label('Value')
                ->options(fn (Get $get): array => static::getValueOptions($get('sis_term_id'), $get('attribute')))
                ->searchable()
                ->required($isRequired)
                ->disabled(fn (Get $get): bool => blank($get('sis_term_id')) || blank($get('attribute')))
                ->placeholder(fn (Get $get): ?string => blank($get('sis_term_id')) || blank($get('attribute')) ? 'Select a term and attribute first' : null),
        ];
    }

    /**
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     *
     * @return Builder<TModel>
     */
    public static function applyToQuery(Builder $query, mixed $termId, mixed $attribute, mixed $value, bool $isInverse = false): Builder
    {
        if (blank($termId) || blank($attribute) || blank($value) || ! is_scalar($termId) || ! is_scalar($value)) {
            return $query;
        }

        $field = static::resolveAttribute($attribute);

        // Only allow-listed columns may reach the query, so an unknown attribute matches no students.
        if (! $field) {
            return $query->whereRaw('1 = 0');
        }

        return $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
            'termAttributes',
            fn (Builder $query) => $query
                ->where('sis_term_id', (string) $termId)
                ->where($field->value, (string) $value),
        );
    }

    public static function getSummary(mixed $termId, mixed $attribute, mixed $value, bool $isInverse = false): string
    {
        $termLabel = is_scalar($termId)
            ? Term::query()->where('sis_term_id', (string) $termId)->first()?->getDisplayName()
            : null;

        $attributeLabel = static::resolveAttribute($attribute)?->getLabel() ?? 'Unknown Attribute';

        $operatorLabel = $isInverse ? 'is not' : 'is';

        $valueLabel = is_scalar($value) ? (string) $value : '';

        return ($termLabel ?? 'Unknown Term') . ": {$attributeLabel} {$operatorLabel} \"{$valueLabel}\"";
    }

    /**
     * @return array<int|string, string>
     */
    public static function getTermOptions(): array
    {
        return Term::query()
            ->whereIn('sis_term_id', StudentTermAttribute::query()->select('sis_term_id'))
            ->orderByRaw('start_date DESC NULLS LAST')
            ->get(['sis_term_id', 'name', 'start_date'])
            ->mapWithKeys(fn (Term $term): array => [$term->sis_term_id => $term->getDisplayName()])
            ->all();
    }

    /**
     * @return array<int|string, string>
     */
    public static function getValueOptions(mixed $termId, mixed $attribute): array
    {
        $field = static::resolveAttribute($attribute);

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

    public static function resolveAttribute(mixed $attribute): ?StudentTermAttributeField
    {
        if ($attribute instanceof StudentTermAttributeField) {
            return $attribute;
        }

        return is_string($attribute) ? StudentTermAttributeField::tryFrom($attribute) : null;
    }
}
