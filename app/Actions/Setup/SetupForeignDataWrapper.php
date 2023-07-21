<?php

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
