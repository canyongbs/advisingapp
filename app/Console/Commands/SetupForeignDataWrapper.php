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

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DataTransferObjects\ForeignDataWrapperData;

class SetupForeignDataWrapper extends Command
{
    protected $signature = 'app:setup-foreign-data-wrapper';

    protected $description = 'Setup foreign data wrapper for SIS database';

    public function handle(): void
    {
        resolve(\App\Actions\Setup\SetupForeignDataWrapper::class)->handle(
            new ForeignDataWrapperData(
                connection: config('database.fdw.connection'),
                localServerName: config('database.fdw.server_name'),
                externalHost: config('database.fdw.external_host'),
                externalPort: config('database.fdw.external_port'),
                externalUser: config('database.fdw.external_user'),
                externalPassword: config('database.fdw.external_password'),
                externalDatabase: config('database.fdw.external_database'),
                tables: [
                    'students',
                    'programs',
                    'enrollments',
                    'performance',
                ],
            )
        );
    }
}
