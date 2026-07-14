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

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InterNACHI\Modular\Support\ModuleConfig;
use InterNACHI\Modular\Support\ModuleRegistry;
use PHPUnit\Framework\Assert;
use Symfony\Component\Finder\SplFileInfo;

arch('All Core Settings classes should have defaults for all properties')
    ->expect('App\Settings')
    ->toHaveDefaultsForAllProperties();

$legacyV4UuidModels = require __DIR__ . '/legacy-v4-uuid-models.php';

arch('All Core Models should not use HasVersion4Uuids trait')
    ->expect('App\Models')
    ->extending(Model::class)
    ->not->toUseTrait(HasVersion4Uuids::class)
    ->ignoring($legacyV4UuidModels);

/** @var Collection<int, ModuleConfig> $modules */
$modulesPath = dirname(__DIR__, 3) . '/app-modules';
$modules = (new ModuleRegistry(
    modules_path: $modulesPath,
    modules_loader: fn () => collect(glob($modulesPath . '/*/composer.json'))
        ->map(fn (string $path) => ModuleConfig::fromComposerFile(
            new SplFileInfo($path, dirname($path), $path)
        ))
        ->keyBy(fn (ModuleConfig $module) => $module->name),
))->modules();

$modules->each(function (ModuleConfig $module) use ($legacyV4UuidModels) {
    arch("All {$module->name} Settings classes should have defaults for all properties")
        ->expect($module->namespace() . 'Settings')
        ->toHaveDefaultsForAllProperties();

    arch("All {$module->name} Models should not use HasVersion4Uuids trait")
        ->expect($module->namespace() . 'Models')
        ->extending(Model::class)
        ->not->toUseTrait(HasVersion4Uuids::class)
        ->ignoring($legacyV4UuidModels);
});

test('Legacy models must not use HasUuids (UUIDv7)', function () {
    $legacyModels = require __DIR__ . '/legacy-v4-uuid-models.php';

    foreach ($legacyModels as $class) {
        $traits = class_uses_recursive($class);

        if (! in_array(HasUuids::class, $traits)) {
            continue;
        }

        Assert::assertContains(
            HasVersion4Uuids::class,
            $traits,
            "Class [{$class}] uses HasUuids (UUIDv7) directly. Legacy models must use HasVersion4Uuids instead.",
        );
    }
});
