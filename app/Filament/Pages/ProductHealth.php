<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Pages;

use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\ResultStore;

class ProductHealth extends HealthCheckResults
{
    protected static ?string $navigationIcon = null;

    public static function getNavigationLabel(): string
    {
        return 'Product Health';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Product Health';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Global Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 80;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = app(ResultStore::class)
            ->latestResults()
            ?->storedCheckResults
            ->filter(fn ($check) => ! in_array($check->status, [Status::ok()->value, Status::skipped()->value]))
            ->count();

        return $count > 0 ? $count : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
    }

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }
}
