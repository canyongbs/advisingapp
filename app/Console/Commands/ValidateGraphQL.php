<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use GraphQL\Type\Introspection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Nuwave\Lighthouse\Schema\AST\ASTCache;
use Nuwave\Lighthouse\Schema\SchemaBuilder;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ValidateGraphQL extends Command implements PromptsForMissingInput
{
    /**
     * @var string
     */
    protected $signature = 'dev:validate-graphql {--model=} {--list}';

    protected $description = 'Validate graphql implementation.';

    public function __construct()
    {
        parent::__construct();

        if ( app()->isProduction()) {
            $this->setHidden();
        }
    }

    public function handle(ASTCache $cache, SchemaBuilder $schemaBuilder): int
    {
        if (app()->isProduction()) {
            $this->error('This command is not available in the production.');

            return self::FAILURE;
        }

        $contents = str(File::get('_ide_helper_models.php'))
            ->explode("\n")
            ->splice(13);

        if (! str($contents->first())->contains('namespace')) {
            $this->error('The first line did not contain "namespace". Please check the "_ide_helper_models.php" file structure.');

            return self::FAILURE;
        }

        $tenant = Tenant::first();

        if (! $tenant) {
            $this->error('No tenant found.');

            return self::FAILURE;
        }

        $tenant->makeCurrent();

        $cache->clear();

        $schema = $schemaBuilder->schema();

        $types = collect(Introspection::fromSchema($schema)['__schema']['types'])->where('kind', 'OBJECT');

        $contents = $contents
            ->map(function (String $line) {
                $line = str($line);

                if (str($line)->contains([
                    '@method',
                    '@mixin',
                    'AllowDynamicProperties',
                    'class IdeHelper',
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

        $fail = false;

        foreach ($contents as $model) {
            $lines = str($model)
                ->explode("\n");

            $class = str($lines->shift());

            $type = $types->where('name', $class->afterLast('\\')->before('::class')->trim())->first();

            if ($type) {
                $this->info("Type found: {$class}");
                $this->line('Description: ' . ($type['description'] ?: $this->style('Missing', 'error')));

                $fields = [];

                foreach ($lines as $line) {
                    $property = str($line)->afterLast('$')->trim();

                    $field = collect($type['fields'])->where('name', $property->snake())->first();

                    if ($field) {
                        if ($field['description']) {
                            $description = $field['description'];
                        } else {
                            $description = $this->style('Missing', 'error');
                            $fail = true;
                        }

                        $this->option('list')
                        ? $this->info("Field found: {$property}")
                        : $fields[] = ['Field' => $this->style($property), 'Description' => $description];
                    } else {
                        $fail = true;
                        $this->option('list')
                        ? $this->warn("Field not found: {$property}")
                        : $fields[] = ['Field' => $this->style($property, 'warning'), 'Description' => $this->style('Not found', 'warning')];
                    }
                }

                if (! $this->option('list')) {
                    $this->table(['Field', 'Description'], $fields);
                }
            } else {
                $this->warn("Type not found: {$class}");
                $fail = true;
            }
            $this->newLine();
        }

        return $fail ? self::FAILURE : self::SUCCESS;
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
