<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Pages;

use App\Models\User;
use Spatie\Health\Enums\Status;
use Illuminate\Contracts\Support\Htmlable;
use Spatie\Health\ResultStores\ResultStore;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults;

class ProductHealth extends HealthCheckResults
{
    public static function getNavigationLabel(): string
    {
        return 'Product Administration';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Product Health Dashboard';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Product Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = app(ResultStore::class)
            ->latestResults()
            ?->storedCheckResults
            ->filter(fn ($check) => $check->status !== Status::ok()->value)
            ->count();

        return $count > 0 ? $count : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('authorization.view_product_health_dashboard');
    }

    public function mount(): void
    {
        $this->authorize('authorization.view_product_health_dashboard');
    }
}
