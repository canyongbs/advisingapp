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

namespace App\Actions\Setup;

use Illuminate\Support\Facades\DB;
use App\DataTransferObjects\ForeignDataWrapperData;

class SetupForeignDataWrapper
{
    public function handle(ForeignDataWrapperData $data): void
    {
        $database = DB::connection($data->connection);

        $database->statement('CREATE EXTENSION IF NOT EXISTS postgres_fdw;');

        $database->statement("
            CREATE SERVER IF NOT EXISTS {$data->localServerName} 
            FOREIGN DATA WRAPPER postgres_fdw
            OPTIONS (host '{$data->externalHost}', dbname '{$data->externalDatabase}', port '{$data->externalPort}');
        ");

        $database->statement("
            CREATE USER MAPPING IF NOT EXISTS FOR CURRENT_USER 
            SERVER {$data->localServerName}
            OPTIONS (user '{$data->externalUser}', password '{$data->externalPassword}');
        ");

        foreach ($data->tables as $table) {
            $tableExists = $database->select("SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE  table_schema = 'public'
                AND    table_name   = '{$table}'
            );")[0]->exists;

            if (! $tableExists) {
                $database->statement("
                    IMPORT FOREIGN SCHEMA public
                    LIMIT TO ({$table})
                    FROM SERVER {$data->localServerName}
                    INTO public;
                ");
            }
        }
    }
}
