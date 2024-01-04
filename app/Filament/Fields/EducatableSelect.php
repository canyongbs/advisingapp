<?php

namespace App\Filament\Fields;

use Closure;
use App\Models\Authenticatable;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Concerns\HasName;
use AdvisingApp\StudentDataModel\Models\Student;
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

    public static function make(string $name): EducatableSelect | MorphToSelect
    {
        if (auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return MorphToSelect::make($name)
                ->searchable()
                ->types([
                    static::getStudentType(),
                    static::getProspectType(),
                ]);
        }

        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getStudentType(): Type
    {
        return MorphToSelect\Type::make(Student::class)
            ->titleAttribute(Student::displayNameKey());
    }

    public static function getProspectType(): Type
    {
        return MorphToSelect\Type::make(Prospect::class)
            ->titleAttribute(Prospect::displayNameKey());
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
