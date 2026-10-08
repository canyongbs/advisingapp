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

namespace Tests\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * Resets a PostgreSQL database between tests by re-cloning it from a template snapshot,
 * for databases that cannot be wrapped in a transaction.
 */
trait RefreshesDatabaseFromSnapshot
{
    /**
     * @param string $connection A connection whose server credentials can create and drop databases.
     */
    protected function snapshotDatabase(string $database, string $connection): void
    {
        $this->cloneDatabase(source: $database, target: $this->databaseSnapshotName($database), connection: $connection);
    }

    /**
     * @param string $connection A connection whose server credentials can create and drop databases.
     */
    protected function restoreDatabaseFromSnapshot(string $database, string $connection): void
    {
        $this->cloneDatabase(source: $this->databaseSnapshotName($database), target: $database, connection: $connection);
    }

    protected function databaseSnapshotName(string $database): string
    {
        return "{$database}_snapshot";
    }

    private function cloneDatabase(string $source, string $target, string $connection): void
    {
        // A template database cannot be cloned, or a database dropped, while anything is connected to it.
        foreach (DB::getConnections() as $openConnection) {
            if (in_array($openConnection->getDatabaseName(), [$source, $target], true)) {
                $openConnection->disconnect();
            }
        }

        $maintenanceConnection = DB::build([
            ...config("database.connections.{$connection}"),
            'database' => 'postgres',
        ]);

        assert($maintenanceConnection instanceof Connection);

        $source = $this->quoteDatabaseIdentifier($source);
        $target = $this->quoteDatabaseIdentifier($target);

        $maintenanceConnection->statement("DROP DATABASE IF EXISTS {$target} WITH (FORCE)");
        $maintenanceConnection->statement("CREATE DATABASE {$target} TEMPLATE {$source} STRATEGY FILE_COPY");
        $maintenanceConnection->disconnect();
    }

    private function quoteDatabaseIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}
