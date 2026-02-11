# Maintaining Group Filters

## Background

Population Groups (internally stored in the `segments` table) can be either **static** or **dynamic**. Dynamic Groups persist their filter configuration as JSON in the `filters` column. This JSON is a serialized representation of the filter constraints defined in `StudentsTable` or `ProspectsTable`, depending on the Group's model type.

When a Group is loaded — whether on an edit page, in a report, or anywhere that translates Group filters into a query — the system deserializes this JSON and attempts to match each stored filter `type` to a constraint class currently registered in the corresponding Table class. This matching happens via `TranslateGroupFilters`, which mounts a Livewire component to reconstruct the filtered query.

## The Problem

If a constraint is removed from `StudentsTable` or `ProspectsTable`, any existing Groups that were created using that constraint will still reference it in their stored `filters` JSON. When the system attempts to load one of these Groups, it tries to resolve a constraint type that no longer exists, which throws an error and prevents the Group from being accessed.

This means that **removing a constraint from either Table class is a breaking change for existing data**. The code change alone is not sufficient — the persisted data must also be cleaned up.

## When a Data Migration Is Needed

A data migration to clean up Group filters is required whenever:

- A filter constraint is **removed** from `StudentsTable::configure()` or `ProspectsTable::configure()`.
- A column is **removed** from the `Student` or `Prospect` model that was backing a filter constraint, which in turn necessitates removing that constraint.

In general, any change that causes a previously valid filter `type` in the stored JSON to become unresolvable requires a data migration.

## How to Write the Data Migration

Data migrations that clean up Group filters should be placed in `app-modules/group/database/migrations/` and follow the temporary migration naming convention:

```
YYYY_MM_DD_HHMMSS_tmp_data_remove_{description}_filters_from_segments.php
```

The migration needs to:

1. Query the `segments` table for rows with non-null `filters`.
2. Decode the JSON and traverse the `queryBuilder.rules` structure.
3. Remove any rules whose `type` matches the obsolete constraint name(s).
4. Handle nested structures — filters can be inside `or` groups, which contain their own `rules` arrays.
5. Update the row only if modifications were actually made.

The constraint `type` stored in the JSON corresponds to the name passed to the constraint's `::make()` call. For example, `BooleanConstraint::make('sms_opt_out')` produces rules with `"type": "sms_opt_out"` in the stored JSON.

For a real-world example, see the migration `2026_02_10_161725_tmp_data_remove_sms_opt_out_and_email_bounce_filters_from_segments.php` in `app-modules/group/database/migrations/`, which was introduced in [PR #2234](https://github.com/canyongbs/advisingapp/pull/2234).

## Testing the Data Migration

Data migration tests are written in `tests/TenantMigrationTests.php` using the `isolatedMigration` helper. A test should:

1. Create Groups with filters that include the obsolete constraint types (both at the top level and nested inside `or` groups).
2. Create edge-case Groups (e.g., with `null` filters or empty filters).
3. Run the migration via `Artisan::call('migrate', ['--path' => '...'])`.
4. Assert that obsolete filter types have been removed.
5. Assert that valid filter types remain untouched.
6. Assert that edge-case Groups are unaffected.

You can see an example of this in the `tests/TenantMigrationTests.php` changes in [PR #2234](https://github.com/canyongbs/advisingapp/pull/2234) as well.

## Temporary Nature

These data migrations are temporary by design. Once the migration has been deployed and executed across all tenants, the migration file and its corresponding test can be removed in a subsequent release cycle.
