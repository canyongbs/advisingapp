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

namespace App\Console\Commands;

use App\Models\Tenant;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use GraphQL\Type\Introspection;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Nuwave\Lighthouse\Console\ValidateSchemaCommand;
use Nuwave\Lighthouse\Schema\AST\ASTCache;
use Nuwave\Lighthouse\Schema\SchemaBuilder;

class ValidateGraphQL extends Command implements PromptsForMissingInput
{
    /**
     * @var string
     */
    protected $signature = 'dev:validate-graphql
        {--descriptions}
        {--details= : Show details: list, table}
        {--model=* : The model to validate}
        {--skip-schema : Skip schema validation}';

    /**
     * @var string
     */
    protected $description = 'Validate graphql implementation.';

    protected string $filename = '_temp_graphql_parse_ide_helper_models.php';

    public function __construct()
    {
        parent::__construct();

        if (app()->isProduction()) {
            $this->setHidden();
        }
    }

    public function handle(ASTCache $cache, SchemaBuilder $schemaBuilder): int
    {
        if (app()->isProduction()) {
            $this->error('This command is not available in production.');

            return static::FAILURE;
        }

        if ($this->option('details') && ! in_array($this->option('details'), ['list', 'table'])) {
            $this->error('The --details option must be one of: list, table.');

            return static::FAILURE;
        }

        $tenant = Tenant::first();

        if (! $tenant) {
            $this->error('No tenant found.');

            return static::FAILURE;
        }

        $tenant->makeCurrent();

        if (! $this->option('skip-schema')) {
            Artisan::call(ValidateSchemaCommand::class, outputBuffer: $this->output);
        } else {
            $this->line($this->style('Schema validation skipped.', 'warning'));
            $cache->clear();
        }

        $this->newLine();

        $schema = $schemaBuilder->schema();

        $types = collect(Introspection::fromSchema($schema)['__schema']['types'])->where('kind', 'OBJECT');

        Artisan::call(ModelsCommand::class, [
            '--filename' => $this->filename,
            '--nowrite' => true,
        ], $this->output);

        $this->newLine();

        $contents = str(File::get($this->filename))
            ->explode("\n")
            ->skipUntil(fn (string $line) => str($line)->contains('namespace'));

        $contents = $contents
            ->map(function (String $line) {
                $line = str($line);

                if (str($line)->contains([
                    '@method',
                    '@mixin',
                    'AllowDynamicProperties',
                    'class IdeHelper',
                    'extends \Eloquent',
                    '/*',
                    '*/',
                ])) {
                    return null;
                }

                if ($line->contains('namespace')) {
                    return '{';
                }

                $line = $line->remove('*')->trim();

                if ($line->startsWith(['App', 'AdvisingApp'])) {
                    $line = $line->prepend('\\')->append('::class');
                }

                if ($line->isEmpty()) {
                    return null;
                }

                return $line;
            })
            ->filter()
            ->join("\n");

        $contents = str($contents)
            ->split('/[{}]/')
            ->map(function (String $line) {
                $line = str($line)->trim();

                if ($line->isEmpty()) {
                    return null;
                }

                return $line;
            })
            ->filter();

        $models = collect($this->option('model'));

        $fail = false;

        foreach ($contents as $block) {
            $this->checkModel($block, $types, $fail, $models);
        }

        File::delete($this->filename);

        $this->line($this->style("File {$this->filename} deleted."));

        if ($fail) {
            return static::FAILURE;
        }

        return static::SUCCESS;
    }

    private function checkModel(string $block, Collection $types, &$fail, Collection $models): void
    {
        $lines = str($block)
            ->explode("\n");

        $class = str($lines->shift());
        $model = $class->afterLast('\\')->before('::class')->trim();

        $type = $types->where('name', $model)->first();

        if ($this->option('model') && $models->isNotEmpty() && $models->doesntContain($model)) {
            return;
        }

        if ($type) {
            $this->info("Type found: {$class}");

            if ($this->option('descriptions')) {
                $this->line('Description: ' . ($type['description'] ?: $this->style('Missing', 'error')));
            }

            $fields = [];

            foreach ($lines as $line) {
                $line = str($line)->trim();
                $property = $line->afterLast('$')->trim();

                $field = collect($type['fields'])->where('name', $property->snake())->first();

                if ($field) {
                    if ($field['description']) {
                        $description = $field['description'];
                    } else {
                        $description = $this->style('Missing', 'error');
                        $fail = true;
                    }

                    $fields[] = ['Field' => $this->style($property), ...$this->option('descriptions') ? ['Description' => $description] : []];

                    if ($this->option('details') === 'list') {
                        $this->info("Field found: {$property}");

                        if ($this->option('descriptions')) {
                            $this->line("Description: {$description}");
                        }
                    }
                } else {
                    $fail = true;

                    $fields[] = ['Field' => $this->style($property, 'warning'), ...$this->option('descriptions') ? ['Description' => $this->style('Not found', 'warning')] : []];

                    if ($this->option('details') === 'list') {
                        $this->warn("Field not found: {$property}");

                        if ($this->option('descriptions')) {
                            $this->line('Description: ' . $this->style('Missing', 'error'));
                        }
                    }
                }
            }

            if ($this->option('details') === 'table') {
                $this->table(['Field', ...$this->option('descriptions') ? ['Description'] : []], $fields);
            }
        } else {
            $this->warn("Type not found: {$class}");
            $fail = true;
        }
        $this->newLine();
    }

    private function style(string $value, string $type = null): string
    {
        return match ($type) {
            'warning' => "\e[33m{$value}\e[39m",
            'error' => "\e[31m{$value}\e[39m",
            default => "\e[32m{$value}\e[39m",
        };
    }
}
