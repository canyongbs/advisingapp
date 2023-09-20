<?php

namespace App\Filament\Actions\ImportAction;

use Closure;
use App\Imports\Importer;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Components\Component;

class ImportColumn extends Component
{
    protected string $name;

    protected string | Closure | null $label = null;

    protected bool | Closure $isMappingRequired = false;

    protected int | Closure | null $decimalPlaces = null;

    protected bool | Closure $isNumeric = false;

    protected bool | Closure $isBoolean = false;

    protected bool | Closure $isBlankStateIgnored = false;

    protected string | Closure | null $arraySeparator = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $guesses = [];

    protected ?Closure $fillRecordUsing = null;

    protected ?Closure $sanitizeStateUsing = null;

    protected array | Closure $dataValidationRules = [];

    protected array | Closure $nestedRecursiveDataValidationRules = [];

    protected ?Importer $importer = null;

    protected mixed $example = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getSelect(): Select
    {
        return Select::make($this->getName())
            ->label($this->label)
            ->placeholder('Select a column')
            ->required($this->isMappingRequired);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string | Closure | null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function example(mixed $example): static
    {
        $this->example = $example;

        return $this;
    }

    public function requiredMapping(bool | Closure $condition = true): static
    {
        $this->isMappingRequired = $condition;

        return $this;
    }

    public function numeric(bool | Closure $condition = true, int | Closure | null $decimalPlaces = null): static
    {
        $this->isNumeric = $condition;
        $this->decimalPlaces = $decimalPlaces;

        return $this;
    }

    public function boolean(bool | Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function ignoreBlankState(bool | Closure $condition = true): static
    {
        $this->isBlankStateIgnored = $condition;

        return $this;
    }

    public function rules(array | Closure $rules): static
    {
        $this->dataValidationRules = $rules;

        return $this;
    }

    public function nestedRecursiveRules(array | Closure $rules): static
    {
        $this->nestedRecursiveDataValidationRules = $rules;

        return $this;
    }

    public function array(string | Closure | null $separator = ','): static
    {
        $this->arraySeparator = $separator;

        return $this;
    }

    /**
     * @param array<string> | Closure $guesses
     */
    public function guess(array | Closure $guesses): static
    {
        $this->guesses = $guesses;

        return $this;
    }

    public function importer(?Importer $importer): static
    {
        $this->importer = $importer;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getGuesses(): array
    {
        $guesses = $this->evaluate($this->guesses);
        array_unshift($guesses, $this->getName());

        return array_reduce($guesses, function (array $carry, string $guess): array {
            $guess = (string) Str::of($guess)
                ->lower()
                ->replace('-', ' ')
                ->replace('_', ' ');
            $carry[] = $guess;

            if (Str::of($guess)->contains(' ')) {
                $carry[] = (string) Str::of($guess)->replace(' ', '-');
                $carry[] = (string) Str::of($guess)->replace(' ', '_');
            }

            return $carry;
        }, []);
    }

    public function sanitizeStateUsing(?Closure $callback): static
    {
        $this->sanitizeStateUsing = $callback;

        return $this;
    }

    public function fillRecordUsing(?Closure $callback): static
    {
        $this->fillRecordUsing = $callback;

        return $this;
    }

    public function sanitizeState(mixed $state, array $options): mixed
    {
        $originalState = $state;

        if (filled($arraySeparator = $this->getArraySeparator())) {
            $state = collect(explode($arraySeparator, strval($state)))
                ->map(fn (mixed $stateItem): mixed => $this->sanitizeStateItem($stateItem))
                ->filter(fn (mixed $stateItem): bool => filled($stateItem))
                ->all();
        } else {
            $state = $this->sanitizeStateItem($state);
        }

        if ($this->sanitizeStateUsing) {
            return $this->evaluate($this->sanitizeStateUsing, [
                'originalState' => $originalState,
                'state' => $state,
                'options' => $options,
            ]);
        }

        return $state;
    }

    public function fillRecord(mixed $state): void
    {
        if ($this->fillRecordUsing) {
            $this->evaluate($this->fillRecordUsing, [
                'state' => $state,
            ]);

            return;
        }

        $this->getImporter()->getRecord()->{$this->getName()} = $state;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDataValidationRules(): array
    {
        return $this->evaluate($this->dataValidationRules);
    }

    public function getNestedRecursiveDataValidationRules(): array
    {
        return $this->evaluate($this->nestedRecursiveDataValidationRules);
    }

    public function isNumeric(): bool
    {
        return (bool) $this->evaluate($this->isNumeric);
    }

    public function isBoolean(): bool
    {
        return (bool) $this->evaluate($this->isBoolean);
    }

    public function isBlankStateIgnored(): bool
    {
        return (bool) $this->evaluate($this->isBlankStateIgnored);
    }

    public function getDecimalPlaces(): ?int
    {
        return $this->evaluate($this->decimalPlaces);
    }

    public function getArraySeparator(): ?string
    {
        return $this->evaluate($this->arraySeparator);
    }

    public function isArray(): bool
    {
        return filled($this->getArraySeparator());
    }

    public function getImporter(): ?Importer
    {
        return $this->importer;
    }

    public function getExample(): mixed
    {
        return $this->evaluate($this->example);
    }

    protected function sanitizeStateItem(mixed $state): mixed
    {
        if (is_string($state)) {
            $state = trim($state);
        }

        if (blank($state)) {
            return null;
        }

        if ($this->isBoolean()) {
            return $this->sanitizeBooleanStateItem($state);
        }

        if ($this->isNumeric()) {
            return $this->sanitizeNumericStateItem($state);
        }

        return $state;
    }

    protected function sanitizeBooleanStateItem(mixed $state): bool
    {
        // Narrow down the possible values of the state to make comparison easier.
        $state = strtolower(strval($state));

        return match ($state) {
            '1', 'true', 'yes', 'y', 'on' => true,
            '0', 'false', 'no', 'n', 'off' => false,
            default => (bool) $state,
        };
    }

    protected function sanitizeNumericStateItem(mixed $state): int | float
    {
        $state = floatval(preg_replace('/[^0-9.]/', '', $state));

        $decimalPlaces = $this->getDecimalPlaces();

        if ($decimalPlaces === null) {
            return $state;
        }

        return round($state, $decimalPlaces);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'data' => [$this->getImporter()->getData()],
            'importer' => [$this->getImporter()],
            'options' => [$this->getImporter()->getOptions()],
            'originalData' => [$this->getImporter()->getOriginalData()],
            'record' => [$this->getImporter()->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Importer::class => [$this->getImporter()],
            Model::class => [$this->getImporter()->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
