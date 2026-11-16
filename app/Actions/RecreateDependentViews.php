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

namespace App\Actions;

use Closure;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Runs a callback that drops or replaces tables, with the views that depend on them dropped first and recreated afterwards.
 *
 * Postgres will not drop a table while a view depends on it, and which views do can vary per tenant (e.g. `student_alerts` is generated from
 * the enabled alerts), so the dependent views are found at runtime, including views that depend on them in turn. It all runs in one
 * transaction, so readers wait for it rather than see a view missing, and a failure leaves the tables and views as they were.
 *
 * Each view is recreated with its definition, options, column defaults, comments, security labels, triggers, rules, privileges and owner.
 * Privileges are recreated as granted by the view's owner, as Postgres only lets the current user be the grantor.
 *
 * Anything else that depends on the tables or views cannot be recreated this way (e.g. materialized views, functions with a `BEGIN ATOMIC`
 * body or that return a view's row type, foreign keys from other tables, or views that are part of an extension), so nothing is dropped
 * and an exception is thrown naming them.
 */
class RecreateDependentViews
{
    /**
     * @template TReturn
     *
     * @param array<string> $tables
     * @param Closure(): TReturn $callback
     *
     * @return TReturn
     */
    public function __invoke(array $tables, Closure $callback): mixed
    {
        return DB::transaction(function () use ($tables, $callback): mixed {
            $views = array_map($this->describeView(...), $this->getDependentViews($tables));

            $this->ensureNothingElseDependsOn($tables, array_column($views, 'oid'));

            foreach ($views as $view) {
                DB::statement("drop view {$view['name']}");
            }

            $result = $callback();

            foreach (array_reverse($views) as $view) {
                $this->recreateView($view);
            }

            return $result;
        });
    }

    /**
     * The views that depend on the tables, directly or through other views, ordered so that each view comes before the views it depends on.
     *
     * @param array<string> $tables
     *
     * @return array<object{oid: int, name: string}>
     */
    private function getDependentViews(array $tables): array
    {
        if (blank($tables)) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($tables), 'to_regclass(?)'));

        // A view's depth is its longest chain of views from the tables, so a view always has a greater depth than the views it depends on.
        return DB::select(<<<SQL
            WITH RECURSIVE dependent_views (oid, depth) AS (
                SELECT DISTINCT pg_rewrite.ev_class, 1
                FROM pg_depend
                JOIN pg_rewrite ON pg_rewrite.oid = pg_depend.objid
                WHERE pg_depend.classid = 'pg_rewrite'::regclass
                    AND pg_depend.refobjid IN ({$placeholders})
                    AND pg_rewrite.ev_class <> pg_depend.refobjid
                UNION
                SELECT pg_rewrite.ev_class, dependent_views.depth + 1
                FROM dependent_views
                JOIN pg_depend ON pg_depend.refobjid = dependent_views.oid
                JOIN pg_rewrite ON pg_rewrite.oid = pg_depend.objid
                WHERE pg_depend.classid = 'pg_rewrite'::regclass
                    AND pg_rewrite.ev_class <> dependent_views.oid
            )
            SELECT
                pg_class.oid::bigint AS oid,
                format('%I.%I', pg_namespace.nspname, pg_class.relname) AS name
            FROM dependent_views
            JOIN pg_class ON pg_class.oid = dependent_views.oid
            JOIN pg_namespace ON pg_namespace.oid = pg_class.relnamespace
            WHERE pg_class.relkind = 'v'
            GROUP BY pg_class.oid, pg_namespace.nspname, pg_class.relname
            ORDER BY max(dependent_views.depth) DESC, pg_class.relname
        SQL, array_values($tables));
    }

    /**
     * @param array<string> $tables
     * @param array<int> $viewOids
     */
    private function ensureNothingElseDependsOn(array $tables, array $viewOids): void
    {
        $tablePlaceholders = implode(', ', array_fill(0, max(count($tables), 1), '?::text'));
        $viewPlaceholders = implode(', ', array_fill(0, max(count($viewOids), 1), '?::oid'));

        // Dependencies of the tables' and views' own parts (e.g. indexes, constraints and triggers) are not `n`ormal ones, so they are not listed.
        $blockers = DB::select(<<<SQL
            WITH
                views AS (
                    SELECT oid FROM unnest(ARRAY[{$viewPlaceholders}]) AS oid WHERE oid IS NOT NULL
                ),
                relations AS (
                    SELECT to_regclass(name)::oid AS oid FROM unnest(ARRAY[{$tablePlaceholders}]) AS name WHERE to_regclass(name) IS NOT NULL
                    UNION
                    SELECT oid FROM views
                ),
                referenced (classid, oid) AS (
                    SELECT 'pg_class'::regclass::oid, oid FROM relations
                    UNION
                    SELECT 'pg_type'::regclass::oid, pg_type.oid FROM pg_type JOIN relations ON pg_type.typrelid = relations.oid
                )
            SELECT DISTINCT CASE
                WHEN pg_rewrite.oid IS NOT NULL THEN pg_describe_object('pg_class'::regclass, pg_rewrite.ev_class, 0)
                ELSE pg_describe_object(pg_depend.classid, pg_depend.objid, pg_depend.objsubid)
            END AS description
            FROM pg_depend
            JOIN referenced ON referenced.classid = pg_depend.refclassid AND referenced.oid = pg_depend.refobjid
            LEFT JOIN pg_rewrite ON pg_depend.classid = 'pg_rewrite'::regclass AND pg_rewrite.oid = pg_depend.objid
            WHERE pg_depend.deptype = 'n'
                AND (pg_rewrite.oid IS NULL OR pg_rewrite.ev_class NOT IN (SELECT oid FROM views))
            UNION
            SELECT format('%s, as it is part of %s', pg_describe_object(pg_depend.classid, pg_depend.objid, 0), pg_describe_object(pg_depend.refclassid, pg_depend.refobjid, 0))
            FROM pg_depend
            WHERE pg_depend.classid = 'pg_class'::regclass
                AND pg_depend.objid IN (SELECT oid FROM views)
                AND pg_depend.deptype = 'e'
            ORDER BY description
        SQL, [...(filled($viewOids) ? $viewOids : [null]), ...(filled($tables) ? $tables : [null])]);

        if (blank($blockers)) {
            return;
        }

        $tableList = implode(', ', $tables);
        $blockerList = implode('; ', array_column($blockers, 'description'));

        throw new RuntimeException("The tables [{$tableList}] cannot be replaced, as these depend on them and cannot be recreated: {$blockerList}.");
    }

    /**
     * Reads everything needed to recreate the view, before it is dropped.
     *
     * @param object{oid: int, name: string} $view
     *
     * @return array{oid: int, name: string, statements: array<string>, hasDefaultPrivileges: bool, tablePrivilegeStatements: array<string>, columnPrivilegeStatements: array<string>, ownerStatement: ?string}
     */
    private function describeView(object $view): array
    {
        $bindings = ['name' => $view->name, 'oid' => $view->oid];

        $statements = [
            DB::scalar(<<<'SQL'
                SELECT format(
                    'CREATE VIEW %s%s AS %s',
                    :name::text,
                    CASE WHEN reloptions IS NULL THEN '' ELSE format(' WITH (%s)', array_to_string(reloptions, ', ')) END,
                    pg_get_viewdef(oid)
                )
                FROM pg_class
                WHERE oid = :oid
            SQL, $bindings),
            ...array_column(DB::select(<<<'SQL'
                SELECT format('ALTER VIEW %s ALTER COLUMN %I SET DEFAULT %s', :name::text, pg_attribute.attname, pg_get_expr(pg_attrdef.adbin, pg_attrdef.adrelid)) AS statement
                FROM pg_attrdef
                JOIN pg_attribute ON pg_attribute.attrelid = pg_attrdef.adrelid AND pg_attribute.attnum = pg_attrdef.adnum
                WHERE pg_attrdef.adrelid = :oid
                ORDER BY pg_attrdef.adnum
            SQL, $bindings), 'statement'),
            ...array_column(DB::select(<<<'SQL'
                SELECT CASE
                    WHEN pg_description.objsubid = 0 THEN format('COMMENT ON VIEW %s IS %L', :name::text, pg_description.description)
                    ELSE format('COMMENT ON COLUMN %s.%I IS %L', :name::text, pg_attribute.attname, pg_description.description)
                END AS statement
                FROM pg_description
                LEFT JOIN pg_attribute ON pg_attribute.attrelid = pg_description.objoid AND pg_attribute.attnum = pg_description.objsubid
                WHERE pg_description.classoid = 'pg_class'::regclass AND pg_description.objoid = :oid
                ORDER BY pg_description.objsubid
            SQL, $bindings), 'statement'),
            ...array_column(DB::select(<<<'SQL'
                SELECT CASE
                    WHEN pg_seclabel.objsubid = 0 THEN format('SECURITY LABEL FOR %I ON VIEW %s IS %L', pg_seclabel.provider, :name::text, pg_seclabel.label)
                    ELSE format('SECURITY LABEL FOR %I ON COLUMN %s.%I IS %L', pg_seclabel.provider, :name::text, pg_attribute.attname, pg_seclabel.label)
                END AS statement
                FROM pg_seclabel
                LEFT JOIN pg_attribute ON pg_attribute.attrelid = pg_seclabel.objoid AND pg_attribute.attnum = pg_seclabel.objsubid
                WHERE pg_seclabel.classoid = 'pg_class'::regclass AND pg_seclabel.objoid = :oid
                ORDER BY pg_seclabel.objsubid, pg_seclabel.provider
            SQL, $bindings), 'statement'),
            ...array_column(DB::select(<<<'SQL'
                SELECT pg_get_triggerdef(oid) AS statement
                FROM pg_trigger
                WHERE tgrelid = :oid AND NOT tgisinternal
                ORDER BY tgname
            SQL, ['oid' => $view->oid]), 'statement'),
            ...array_column(DB::select(<<<'SQL'
                SELECT pg_get_ruledef(oid) AS statement
                FROM pg_rewrite
                WHERE ev_class = :oid AND rulename <> '_RETURN'
                ORDER BY rulename
            SQL, ['oid' => $view->oid]), 'statement'),
        ];

        // The owner's own privileges are granted to the current user, who creates the view, and pass to the owner when it is reassigned.
        $tablePrivilegeStatements = array_column(DB::select(<<<'SQL'
            SELECT format(
                'GRANT %s ON %s TO %s%s',
                privileges.privilege_type,
                :name::text,
                CASE
                    WHEN privileges.grantee = 0 THEN 'PUBLIC'
                    WHEN privileges.grantee = pg_class.relowner THEN 'CURRENT_USER'
                    ELSE quote_ident(pg_get_userbyid(privileges.grantee))
                END,
                CASE WHEN privileges.is_grantable THEN ' WITH GRANT OPTION' ELSE '' END
            ) AS statement
            FROM pg_class, aclexplode(coalesce(pg_class.relacl, acldefault('r', pg_class.relowner))) AS privileges
            WHERE pg_class.oid = :oid
        SQL, $bindings), 'statement');

        $columnPrivilegeStatements = array_column(DB::select(<<<'SQL'
            SELECT format(
                'GRANT %s (%I) ON %s TO %s%s',
                privileges.privilege_type,
                pg_attribute.attname,
                :name::text,
                CASE
                    WHEN privileges.grantee = 0 THEN 'PUBLIC'
                    WHEN privileges.grantee = pg_class.relowner THEN 'CURRENT_USER'
                    ELSE quote_ident(pg_get_userbyid(privileges.grantee))
                END,
                CASE WHEN privileges.is_grantable THEN ' WITH GRANT OPTION' ELSE '' END
            ) AS statement
            FROM pg_class
            JOIN pg_attribute ON pg_attribute.attrelid = pg_class.oid
            CROSS JOIN aclexplode(pg_attribute.attacl) AS privileges
            WHERE pg_class.oid = :oid AND pg_attribute.attacl IS NOT NULL
            ORDER BY pg_attribute.attnum
        SQL, $bindings), 'statement');

        $ownership = DB::selectOne(<<<'SQL'
            SELECT
                relacl IS NULL AS has_default_privileges,
                CASE WHEN pg_get_userbyid(relowner) <> current_user THEN format('ALTER VIEW %s OWNER TO %I', :name::text, pg_get_userbyid(relowner)) END AS owner_statement
            FROM pg_class
            WHERE oid = :oid
        SQL, $bindings);

        return [
            ...$bindings,
            'statements' => $statements,
            'hasDefaultPrivileges' => $ownership->has_default_privileges,
            'tablePrivilegeStatements' => $tablePrivilegeStatements,
            'columnPrivilegeStatements' => $columnPrivilegeStatements,
            'ownerStatement' => $ownership->owner_statement,
        ];
    }

    /**
     * @param array{oid: int, name: string, statements: array<string>, hasDefaultPrivileges: bool, tablePrivilegeStatements: array<string>, columnPrivilegeStatements: array<string>, ownerStatement: ?string} $view
     */
    private function recreateView(array $view): void
    {
        foreach ($view['statements'] as $statement) {
            DB::statement($statement);
        }

        $hasDefaultPrivileges = (bool) DB::scalar('SELECT relacl IS NULL FROM pg_class WHERE oid = to_regclass(?)', [$view['name']]);

        // The new view can be given privileges by default (e.g. `ALTER DEFAULT PRIVILEGES`), so they are revoked before the old ones are granted.
        if (! ($view['hasDefaultPrivileges'] && $hasDefaultPrivileges)) {
            $revokeStatements = array_column(DB::select(<<<'SQL'
                SELECT DISTINCT format(
                    'REVOKE ALL ON %s FROM %s',
                    :name::text,
                    CASE WHEN privileges.grantee = 0 THEN 'PUBLIC' ELSE quote_ident(pg_get_userbyid(privileges.grantee)) END
                ) AS statement
                FROM pg_class, aclexplode(coalesce(pg_class.relacl, acldefault('r', pg_class.relowner))) AS privileges
                WHERE pg_class.oid = to_regclass(:name)
            SQL, ['name' => $view['name']]), 'statement');

            foreach ([...$revokeStatements, ...$view['tablePrivilegeStatements']] as $statement) {
                DB::statement($statement);
            }
        }

        foreach ($view['columnPrivilegeStatements'] as $statement) {
            DB::statement($statement);
        }

        if (filled($view['ownerStatement'])) {
            DB::statement($view['ownerStatement']);
        }
    }
}
