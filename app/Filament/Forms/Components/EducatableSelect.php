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

namespace App\Filament\Forms\Components;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Authenticatable;
use App\Models\Scopes\ExcludeConvertedProspects;
use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasName;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EducatableSelect extends Component
{
    use HasName;

    protected bool | Closure $isRequired = false;

    protected string $view = 'filament-forms::components.group';

    protected $isExcludingConvertedProspects = false;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public function excludeConvertedProspects(bool $isExcludingConvertedProspects = true): void
    {
        $this->isExcludingConvertedProspects = $isExcludingConvertedProspects;
    }

    public static function make(string $name, bool $isExcludingConvertedProspects = true): EducatableSelect | MorphToSelect
    {
        if (auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return MorphToSelect::make($name)
                ->searchable()
                ->types(fn (?Model $record, MorphToSelect $component) => [
                    static::getStudentType(),
                    static::getProspectType($component->getRelationship()->getForeignKeyName(), $isExcludingConvertedProspects, $record),
                ]);
        }

        $static = app(static::class, ['name' => $name]);
        $static->configure();

        $static->excludeConvertedProspects($isExcludingConvertedProspects);

        return $static;
    }

    public static function getStudentType(): Type
    {
        return Type::make(Student::class)
            ->titleAttribute(Student::displayNameKey());
    }

    public static function getProspectType(string $keyColumnName, $isExcludingConvertedProspects = true, ?Model $record = null): Type
    {
        $prospectType = Type::make(Prospect::class)
            ->titleAttribute(Prospect::displayNameKey());

        if ($isExcludingConvertedProspects) {
            $prospectType->modifyOptionsQueryUsing(function (Builder $query) use ($keyColumnName, $record) {
                $query->tap(new ExcludeConvertedProspects());

                if ($record) {
                    $query->orWhere('id', $record->{$keyColumnName});
                }
            });
        }

        return $prospectType;
    }

    public function getChildComponents(): array
    {
        /** @var Authenticatable $user */
        $user = auth()->user();

        $relationship = $this->getRelationship();

        $type = match (true) {
            $user->hasLicense(Student::getLicenseType()) => static::getStudentType(),
            $user->hasLicense(Prospect::getLicenseType()) => static::getProspectType(
                $relationship->getForeignKeyName(),
                $this->isExcludingConvertedProspects,
                $this->getRecord()
            ),
            default => null,
        };

        if (! $type) {
            return [];
        }

        return [
            Hidden::make($relationship->getMorphType())
                ->dehydrateStateUsing(fn (): string => $type->getAlias()),
            Select::make($relationship->getForeignKeyName())
                ->label($this->getLabel())
                ->options($type->getOptionsUsing)
                ->getSearchResultsUsing($type->getSearchResultsUsing)
                ->getOptionLabelUsing($type->getOptionLabelUsing)
                ->required($this->isRequired())
                ->searchable()
                ->afterStateUpdated(function () {
                    $this->callAfterStateUpdated();
                }),
        ];
    }

    public function required(bool | Closure $condition = true): static
    {
        $this->isRequired = $condition;

        return $this;
    }

    public function getRelationship(): MorphTo
    {
        return $this->getModelInstance()->{$this->getName()}();
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->isRequired);
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        if (! $this->isRequired()) {
            return false;
        }

        return ! auth()->user()->hasAnyLicense([Student::getLicenseType(), Prospect::getLicenseType()]);
    }
}
