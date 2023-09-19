<?php

namespace App\Imports;

use App\Models\Import;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Filament\Actions\ImportAction\ImportColumn;
use Illuminate\Queue\Middleware\WithoutOverlapping;

abstract class Importer
{
    protected array $cachedColumns;

    protected array $originalData;

    protected array $data;

    protected ?Model $record;

    protected static ?string $model = null;

    /**
     * @param array<string, string> $columnMap
     * @param array<string, mixed> $options
     */
    public function __construct(
        readonly protected Import $import,
        readonly protected array $columnMap,
        readonly protected array $options,
    ) {}

    public function __invoke(array $data): void
    {
        $this->originalData = $this->data = $data;
        $this->record = null;

        $this->remapData();
        $this->sanitizeData();

        $this->record = $this->resolveRecord();

        if (! $this->record) {
            return;
        }

        $this->callHook('beforeValidate');

        try {
            $this->validateData();
        } catch (ValidationException $exception) {
            Notification::make()
                ->title($this->getValidationFailureNotificationTitle($exception->getMessage()))
                ->body($this->getValidationFailureNotificationBody($exception->getMessage()))
                ->danger()
                ->sendToDatabase($this->import->user);

            return;
        }
        $this->callHook('afterValidate');

        $this->callHook('beforeFill');
        $this->fillRecord();
        $this->callHook('afterFill');

        $recordExists = $this->record->exists;

        $this->callHook('beforeSave');
        $this->callHook($recordExists ? 'beforeUpdate' : 'beforeCreate');
        $this->saveRecord();
        $this->callHook('afterSave');
        $this->callHook($recordExists ? 'afterUpdate' : 'afterCreate');
    }

    public function remapData(): void
    {
        $data = $this->data;

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();
            $rowColumnName = $this->columnMap[$columnName] ?? null;

            if (! array_key_exists($rowColumnName, $this->data)) {
                continue;
            }

            $data[$columnName] = $this->data[$rowColumnName];
        }

        $this->data = $data;
    }

    public function sanitizeData(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $this->data[$columnName] = $column->sanitizeState(
                $this->data[$columnName],
                $this->options,
            );
        }
    }

    public function resolveRecord(): ?Model
    {
        $keyName = app(static::getModel())->getKeyName();
        $keyColumnName = $this->columnMap[$keyName] ?? $keyName;

        return static::getModel()::find($this->data[$keyColumnName]);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(): void
    {
        $validator = Validator::make(
            $this->data,
            $this->getValidationRules(),
            $this->getValidationMessages(),
            $this->getValidationAttributes(),
        );

        $validator->validate();
    }

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            $rules[$columnName] = $column->getDataValidationRules();

            if (
                $column->isArray() &&
                count($nestedRecursiveRules = $column->getNestedRecursiveDataValidationRules())
            ) {
                $rules["{$columnName}.*"] = $nestedRecursiveRules;
            }
        }

        return $rules;
    }

    public function getValidationMessages(): array
    {
        return [];
    }

    public function getValidationAttributes(): array
    {
        return [];
    }

    public function fillRecord(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $state = $this->data[$columnName];

            if (blank($state) && $column->isBlankStateIgnored()) {
                continue;
            }

            $column->fillRecord($state);
        }
    }

    public function saveRecord(): void
    {
        $this->record->save();
    }

    /**
     * @return array<ImportColumn>
     */
    abstract public static function getColumns(): array;

    /**
     * @return array<Component>
     */
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    /**
     * @return class-string<Model>
     */
    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Importer')
            ->prepend('App\\Models\\');
    }

    abstract public static function getCompletedNotificationBody(int $totalRows): string;

    abstract public static function getFailureNotificationBody(int $processedRows): string;

    public function getValidationFailureNotificationTitle(string $message): string
    {
        return 'Import validation error';
    }

    abstract public function getValidationFailureNotificationBody(string $message): string;

    /**
     * @return array<int, object>
     */
    public function getJobMiddleware(): array
    {
        return [
            (new WithoutOverlapping("import{$this->import->id}"))->expireAfter(600),
        ];
    }

    public function getJobRetryUntil(): CarbonInterface
    {
        return now()->addDay();
    }

    /**
     * @return array<int, string>
     */
    public function getJobTags(): array
    {
        return ["import{$this->import->id}"];
    }

    public function getCachedColumns(): array
    {
        return $this->cachedColumns ??= array_map(
            fn (ImportColumn $column) => $column->importer($this),
            static::getColumns(),
        );
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
