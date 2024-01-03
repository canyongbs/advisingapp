<?php

namespace App\Filament\Fields;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Closure;
use Exception;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasName;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
        if (auth()->user()->canAccessStudents() && auth()->user()->canAccessProspects()) {
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
        $relationship = $this->getRelationship();
        $typeColumn = $relationship->getMorphType();
        $keyColumn = $relationship->getForeignKeyName();

        $canAccessStudents = auth()->user()->canAccessStudents();
        $canAccessProspects = auth()->user()->canAccessProspects();

        $type = match (true) {
            $canAccessStudents => static::getStudentType(),
            $canAccessProspects => static::getProspectType(),
            default => null,
        };

        if (! $type) {
            return [];
        }

        return [
            Hidden::make($typeColumn)
                ->dehydrateStateUsing(fn (): string => $type->getAlias()),
            Select::make($keyColumn)
                ->label($this->getLabel())
                ->options($type->getOptionsUsing)
                ->getSearchResultsUsing($type->getSearchResultsUsing)
                ->getOptionLabelUsing($type->getOptionLabelUsing)
                ->required($this->isRequired())
                ->searchable(),
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

        return ! (auth()->user()->canAccessProspects() || auth()->user()->canAccessStudents());
    }
}
