<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

use Illuminate\Console\Command;
use App\Actions\Setup\SetupAdmMaterializedViews;

class CreateAdmMaterializedViews extends Command
{
    protected $signature = 'app:create-adm-materialized-views';

    protected $description = 'Creates the materialized views for the ADM tables';

    public function handle(): void
    {
        if (! config('database.adm_materialized_views_enabled')) {
            $this->warn('ADM materialized views are not enabled, skipping...');

            return;
        }

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'students',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'programs',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'enrollments',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'performance',
            indexColumn: 'sisid',
        );
    }
}
