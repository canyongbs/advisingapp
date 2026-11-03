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

use AdvisingApp\Prospect\Imports\ProspectImporter;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Filament\Actions\Imports\ImportColumn;

/**
 * The 'name' columns are citext, so a case-insensitive lookup would let a legacy import
 * name that the name citext migration renamed (e.g. a "referral" renamed to "referral-2")
 * silently resolve to a different, retained row with the same name in another case. The
 * importer resolves these relationships by exact, case-sensitive name to prevent that.
 */
$resolveImportColumn = function (string $name, mixed $state): mixed {
    $column = collect(ProspectImporter::getColumns())
        ->first(fn (ImportColumn $column): bool => $column->getName() === $name);
    $resolveUsing = (new ReflectionProperty(ImportColumn::class, 'resolveRelationshipUsing'))
        ->getValue($column);

    return $resolveUsing($state);
};

describe('source resolution', function () use ($resolveImportColumn): void {
    it('resolves a source by its exact name', function () use ($resolveImportColumn): void {
        $source = ProspectSource::factory()->create(['name' => 'Referral']);

        expect($resolveImportColumn('source', 'Referral')?->getKey())->toBe($source->getKey());
    });

    it('resolves a source by its id', function () use ($resolveImportColumn): void {
        $source = ProspectSource::factory()->create(['name' => 'Referral']);

        expect($resolveImportColumn('source', $source->getKey())?->getKey())->toBe($source->getKey());
    });

    it('does not resolve a source by a different casing of its name', function () use ($resolveImportColumn): void {
        ProspectSource::factory()->create(['name' => 'Referral']);

        expect($resolveImportColumn('source', 'referral'))->toBeNull();
    });
});

describe('status resolution', function () use ($resolveImportColumn): void {
    it('resolves a status by its exact name', function () use ($resolveImportColumn): void {
        $status = ProspectStatus::factory()->create(['name' => 'Engaged']);

        expect($resolveImportColumn('status', 'Engaged')?->getKey())->toBe($status->getKey());
    });

    it('resolves a status by its id', function () use ($resolveImportColumn): void {
        $status = ProspectStatus::factory()->create(['name' => 'Engaged']);

        expect($resolveImportColumn('status', $status->getKey())?->getKey())->toBe($status->getKey());
    });

    it('does not resolve a status by a different casing of its name', function () use ($resolveImportColumn): void {
        ProspectStatus::factory()->create(['name' => 'Engaged']);

        expect($resolveImportColumn('status', 'engaged'))->toBeNull();
    });
});
