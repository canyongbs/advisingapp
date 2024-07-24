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

namespace FilamentTiptapEditor\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

use Filament\Support\Commands\Concerns\CanManipulateFiles;

class MakeBlockCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Create a new Tiptap Editor block';

    protected $signature = 'make:tiptap-block {name?} {--F|force}';

    public function handle(): int
    {
        $block = (string) str(
            $this->argument('name') ??
                text(
                    label: 'What is the block name?',
                    placeholder: 'CustomBlock',
                    required: true,
                ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $blockClass = (string) str($block)->afterLast('\\');
        $blockNamespace = str($block)->contains('\\')
            ? (string) str($block)->beforeLast('\\')
            : '';

        $namespace = 'App\\TiptapBlocks';

        $path = app_path('TiptapBlocks/');

        $preview = str($block)
            ->prepend(
                (string) str("{$namespace}\\Previews\\")
                    ->replaceFirst('App\\', '')
            )
            ->replace('\\', '/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $rendered = str($block)
            ->prepend(
                (string) str("{$namespace}\\Rendered\\")
                    ->replaceFirst('App\\', '')
            )
            ->replace('\\', '/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $path = (string) str($block)
            ->prepend('/')
            ->prepend($path ?? '')
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $previewPath = resource_path(
            (string) str($preview)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $renderedViewPath = resource_path(
            (string) str($rendered)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $files = [
            $path,
            $previewPath,
            $renderedViewPath,
        ];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('Block', $path, [
            'class' => $blockClass,
            'namespace' => str($namespace) . ($blockNamespace !== '' ? "\\{$blockNamespace}" : ''),
            'preview' => $preview,
            'rendered' => $rendered,
        ]);

        $this->copyStubToApp('Preview', $previewPath);

        $this->copyStubToApp('Rendered', $renderedViewPath);

        $this->components->info("Tiptap Editor Block [{$path}] created successfully.");

        return self::SUCCESS;
    }
}
