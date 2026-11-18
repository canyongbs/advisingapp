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

use App\Actions\RecreateDependentViews;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function (): void {
    DB::statement('create table recreate_views_source (id integer)');
    DB::statement('insert into recreate_views_source values (1)');
    DB::statement('create table recreate_views_unrelated (id integer)');

    DB::statement('create view recreate_views_direct as select id from recreate_views_source');
    DB::statement('create view recreate_views_nested as select id * 10 as id from recreate_views_direct');
    DB::statement('create view recreate_views_joined as select recreate_views_source.id from recreate_views_source join recreate_views_nested on recreate_views_nested.id = recreate_views_source.id * 10');
    DB::statement('create view recreate_views_independent as select id from recreate_views_unrelated');
});

$replaceSourceTable = function (): void {
    DB::statement('create table recreate_views_replacement (id integer)');
    DB::statement('insert into recreate_views_replacement values (2)');
    DB::statement('drop table recreate_views_source');
    DB::statement('alter table recreate_views_replacement rename to recreate_views_source');
};

it('recreates the views that depend on a replaced table, directly or through other views', function () use ($replaceSourceTable): void {
    $independentViewOid = DB::scalar("select to_regclass('recreate_views_independent')::oid");

    $result = app(RecreateDependentViews::class)(['recreate_views_source'], function () use ($replaceSourceTable): string {
        $replaceSourceTable();

        return 'replaced';
    });

    expect($result)->toBe('replaced')
        ->and(DB::table('recreate_views_direct')->pluck('id')->all())->toBe([2])
        ->and(DB::table('recreate_views_nested')->pluck('id')->all())->toBe([20])
        ->and(DB::table('recreate_views_joined')->pluck('id')->all())->toBe([2])
        ->and(DB::scalar("select to_regclass('recreate_views_independent')::oid"))->toBe($independentViewOid);
});

it('leaves the table and its views as they were when the callback fails', function () use ($replaceSourceTable): void {
    expect(fn () => app(RecreateDependentViews::class)(['recreate_views_source'], function () use ($replaceSourceTable): void {
        $replaceSourceTable();

        throw new RuntimeException('The replacement failed.');
    }))->toThrow(RuntimeException::class, 'The replacement failed.');

    expect(DB::table('recreate_views_source')->pluck('id')->all())->toBe([1])
        ->and(DB::table('recreate_views_nested')->pluck('id')->all())->toBe([10])
        ->and(DB::table('recreate_views_joined')->pluck('id')->all())->toBe([1]);
});

it('ignores tables that do not exist', function (): void {
    app(RecreateDependentViews::class)(['recreate_views_missing'], fn () => null);

    expect(DB::table('recreate_views_nested')->pluck('id')->all())->toBe([10]);
});

it('recreates each view with its options, column defaults, comments, triggers, rules, privileges and owner', function () use ($replaceSourceTable): void {
    $reader = 'recreate_views_reader_' . Str::lower(Str::random(8));
    $owner = 'recreate_views_owner_' . Str::lower(Str::random(8));

    DB::statement("create role {$reader}");
    DB::statement("create role {$owner}");

    DB::statement('alter view recreate_views_direct set (security_barrier = true, check_option = local)');
    DB::statement('alter view recreate_views_direct alter column id set default 5');
    DB::statement("comment on view recreate_views_direct is 'The direct view'");
    DB::statement("comment on column recreate_views_direct.id is 'The ID'");
    DB::statement('create function recreate_views_insert() returns trigger language plpgsql as $$ begin return new; end $$');
    DB::statement('create trigger recreate_views_insert instead of insert on recreate_views_direct for each row execute function recreate_views_insert()');
    DB::statement('create rule recreate_views_delete as on delete to recreate_views_direct do instead nothing');
    DB::statement("grant select on recreate_views_direct to {$reader} with grant option");
    DB::statement("grant update (id) on recreate_views_direct to {$reader}");
    DB::statement('grant select on recreate_views_direct to public');
    DB::statement("alter view recreate_views_nested owner to {$owner}");

    $describeView = fn (string $view): array => (array) DB::selectOne(<<<'SQL'
        SELECT
            pg_class.reloptions::text AS options,
            pg_get_userbyid(pg_class.relowner) AS owner,
            (
                SELECT string_agg(format('%s>%s:%s:%s', pg_get_userbyid(privileges.grantor), pg_get_userbyid(privileges.grantee), privileges.privilege_type, privileges.is_grantable), ',' ORDER BY 1)
                FROM aclexplode(coalesce(pg_class.relacl, acldefault('r', pg_class.relowner))) AS privileges
            ) AS privileges,
            (SELECT string_agg(format('%s=%s', attname, attacl), ',' ORDER BY attnum) FROM pg_attribute WHERE attrelid = pg_class.oid AND attnum > 0) AS column_privileges,
            obj_description(pg_class.oid, 'pg_class') AS comment,
            (SELECT string_agg(format('%s=%s', attname, col_description(pg_class.oid, attnum)), ',' ORDER BY attnum) FROM pg_attribute WHERE attrelid = pg_class.oid AND attnum > 0) AS column_comments,
            (SELECT string_agg(pg_get_expr(adbin, adrelid), ',' ORDER BY adnum) FROM pg_attrdef WHERE adrelid = pg_class.oid) AS column_defaults,
            (SELECT string_agg(pg_get_triggerdef(oid), ',' ORDER BY tgname) FROM pg_trigger WHERE tgrelid = pg_class.oid AND NOT tgisinternal) AS triggers,
            (SELECT string_agg(pg_get_ruledef(oid), ',' ORDER BY rulename) FROM pg_rewrite WHERE ev_class = pg_class.oid AND rulename <> '_RETURN') AS rules
        FROM pg_class
        WHERE pg_class.oid = to_regclass(?)
    SQL, [$view]);

    $directViewBefore = $describeView('recreate_views_direct');
    $nestedViewBefore = $describeView('recreate_views_nested');

    app(RecreateDependentViews::class)(['recreate_views_source'], $replaceSourceTable);

    expect($directViewBefore)
        ->options->toBe('{security_barrier=true,check_option=local}')
        ->privileges->toContain("{$reader}:SELECT:t")
        ->column_privileges->toContain($reader)
        ->triggers->toContain('recreate_views_insert')
        ->rules->toContain('recreate_views_delete')
        ->and($nestedViewBefore['owner'])->toBe($owner)
        ->and($describeView('recreate_views_direct'))->toBe($directViewBefore)
        ->and($describeView('recreate_views_nested'))->toBe($nestedViewBefore)
        ->and(DB::table('recreate_views_nested')->pluck('id')->all())->toBe([20]);
});

it('does not replace the tables when something that cannot be recreated depends on them', function (array $statements, string $description): void {
    foreach ($statements as $statement) {
        DB::statement($statement);
    }

    $hasRunCallback = false;

    expect(fn () => app(RecreateDependentViews::class)(['recreate_views_source'], function () use (&$hasRunCallback): void {
        $hasRunCallback = true;
    }))->toThrow(RuntimeException::class, $description);

    expect($hasRunCallback)->toBeFalse()
        ->and(DB::table('recreate_views_nested')->pluck('id')->all())->toBe([10]);
})->with([
    'a materialized view' => [
        ['create materialized view recreate_views_materialized as select id from recreate_views_nested'],
        'materialized view recreate_views_materialized',
    ],
    'a function with a `BEGIN ATOMIC` body' => [
        ['create function recreate_views_count() returns bigint language sql begin atomic select count(*) from recreate_views_direct; end'],
        'function recreate_views_count()',
    ],
    'a function returning a view\'s rows' => [
        ['create function recreate_views_rows() returns setof recreate_views_direct language sql as $$ select * from recreate_views_direct $$'],
        'function recreate_views_rows()',
    ],
    'a foreign key from another table' => [
        [
            'alter table recreate_views_source add primary key (id)',
            'create table recreate_views_referencing (source_id integer references recreate_views_source (id))',
        ],
        'on table recreate_views_referencing',
    ],
]);
