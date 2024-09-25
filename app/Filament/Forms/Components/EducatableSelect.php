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

use Closure;
use App\Models\Authenticatable;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Concerns\HasName;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Scopes\ExcludeConvertedProspects;
use Filament\Forms\Components\MorphToSelect\Type;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EducatableSelect extends Component
{
    use HasName;

    protected bool | Closure $isRequired = false;

    protected string $view = 'filament-forms::components.group';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name, $includedRecord = null, bool $isExcludingConvertedProspects = false): EducatableSelect | MorphToSelect
    {
        if (auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return MorphToSelect::make($name)
                ->searchable()
                ->types([
                    static::getStudentType(),
                    static::getProspectType($isExcludingConvertedProspects, $includedRecord),
                ]);
        }

        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getStudentType(): Type
    {
        return Type::make(Student::class)
            ->titleAttribute(Student::displayNameKey());
    }

    public static function getProspectType($isExcludingConvertedProspects = false, ?Prospect $includedRecord = null): Type
    {
        $prospectType = Type::make(Prospect::class)
            ->titleAttribute(Prospect::displayNameKey());

        $prospectType->modifyOptionsQueryUsing(function (Builder $query) use ($isExcludingConvertedProspects, $includedRecord) {
            $query->when($isExcludingConvertedProspects, function (Builder $query) {
                return $query->tap(new ExcludeConvertedProspects());
            });

            $query->when($includedRecord, function (Builder $query) use ($includedRecord) {
                Log::debug($includedRecord);

                return $query->orWhere('id', $includedRecord->getKey());
            });
        });

        return $prospectType;
    }

    public function getChildComponents(): array
    {
        /** @var Authenticatable $user */
        $user = auth()->user();

        $type = match (true) {
            $user->hasLicense(Student::getLicenseType()) => static::getStudentType(),
            $user->hasLicense(Prospect::getLicenseType()) => static::getProspectType(),
            default => null,
        };

        if (! $type) {
            return [];
        }

        $relationship = $this->getRelationship();

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
