---
status: 'accepted'
date: 2026-03-18
decision-makers: Dan Harrin, Kevin Ullyott
consulted: Dan Harrin, Kevin Ullyott
---

# Resolving VARCHAR and UUID Type Mismatches in Polymorphic Relationships

## Context and Problem Statement

Our application uses PostgreSQL 15 and has a mix of primary key types across models. The `Student` model uses a string primary key (`sisid`) since student data is synced from external student information systems where we have no control over the identifying key format. Most other models use UUID primary keys.

Several models participate in polymorphic relationships alongside `Student`—most notably `Prospect` and `CaseModel`. These polymorphic relationships store the related model's ID in a `varchar` morph column (e.g., `respondent_id`). When PostgreSQL needs to compare a `varchar` morph column with a `uuid` primary key column (e.g., in EXISTS subqueries or WHERE IN clauses), it fails because there is no implicit cast between the two types.

Early in the project, we worked around this by creating an implicit PostgreSQL cast from `VARCHAR` to `UUID`:

```sql
CREATE CAST (VARCHAR AS uuid) WITH INOUT AS IMPLICIT
```

This cast can no longer be created in our staging and production environments due to infrastructure constraints, so we need an alternative solution.

## Considered Options

- Convert polymorphic models' primary keys from `uuid` to `text`
- Override `qualifyColumn()` / `getQualifiedKeyName()` on affected models
- Override specific relationship factory methods per model
- Create a custom PostgreSQL query grammar subclass

## Decision Outcome

Chosen option: "Convert polymorphic models' primary keys from `uuid` to `text`", because it eliminates the type mismatch entirely at the database level with zero ongoing maintenance burden, no coupling to Laravel internals, and no risk of breakage across Laravel upgrades.

The affected models are `Prospect` and `CaseModel`—the only two UUID-keyed models that share polymorphic relationships with `Student`. Their primary key columns are changed from `uuid` to `text`, along with all foreign key columns that reference them. The models continue to use Laravel's `HasVersion4Uuids` trait to generate UUID values; only the PostgreSQL column type changes.

Additionally, `Student`'s `primary_email_id`, `primary_phone_id`, and `primary_address_id` columns are changed from `varchar` to `uuid` to match the UUID primary keys of the tables they reference (`student_email_addresses`, `student_phone_numbers`, `student_addresses`). These were originally created as `string` columns but store UUID values.

### Consequences

- Good, because all `varchar = uuid` comparisons become `text = text`, eliminating the type mismatch without any casting
- Good, because it requires no changes to application code, model definitions, or relationship declarations
- Good, because it has zero coupling to Laravel framework internals and is unaffected by Laravel version upgrades
- Good, because stored values remain valid UUIDs—only the PostgreSQL column type changes
- Good, because the implicit cast migration can be removed, preventing issues for new tenants in environments where it cannot be created
- Bad, because UUID columns lose PostgreSQL's native UUID type validation at the database level
- Neutral, because a database migration must run to alter column types and temporarily drop/recreate foreign key constraints

### Confirmation

Compliance with this decision can be confirmed through:

1. **Migration verification**: The migration at `database/migrations/2026_03_18_163527_change_morphed_id_columns_from_uuid_to_string.php` converts `prospects.id` and `cases.id` to `text` and updates all related foreign key columns
2. **Implicit cast removal**: The migration at `app-modules/student-data-model/database/migrations/2023_08_11_235451_create_implicit_varchar_as_uuid_cast.php` has been deleted
3. **Test suite**: All existing tests pass without the implicit cast, confirming no `varchar = uuid` comparison errors remain

## Pros and Cons of the Options

### Convert Polymorphic Models' Primary Keys from `uuid` to `text`

Change the PostgreSQL column type of `prospects.id` and `cases.id` from `uuid` to `text`, along with all foreign key columns referencing them. Models continue generating UUID values via Laravel's `HasVersion4Uuids` trait.

- Good, because it eliminates the root cause—type mismatches—at the database level
- Good, because no application code changes are needed
- Good, because it is a one-time migration with no ongoing maintenance
- Good, because it is completely decoupled from Laravel internals
- Bad, because the database no longer validates that values are well-formed UUIDs
- Bad, because foreign key constraints must be temporarily dropped and recreated during migration

### Override `qualifyColumn()` / `getQualifiedKeyName()` on Affected Models

Override these methods on `Prospect` and `CaseModel` to return `DB::raw('"table"."id"::text')`, causing all SQL references to the primary key to include an explicit `::text` cast.

- Good, because the database schema remains unchanged
- Bad, because it breaks ORDER BY, GROUP BY, UPDATE queries, route model binding, column aliases, and at least 8 other usage patterns where Laravel passes the qualified column name in non-WHERE contexts
- Bad, because it is fragile and tightly coupled to Laravel's internal query building

### Override Specific Relationship Factory Methods per Model

Override `morphMany()`, `newMorphTo()`, or `getRelationExistenceQuery()` on individual models to inject `::text` casts into specific query contexts.

- Good, because changes are scoped to specific relationship types
- Bad, because each override only fixes one of the three failing query patterns (eager loading, lazy loading, whereHasMorph)
- Bad, because `MorphTo` is defined on the child model, requiring changes to every model with a `morphTo` that could resolve to the affected models
- Bad, because new relationships added in the future could be missed

### Create a Custom PostgreSQL Query Grammar Subclass

Extend Laravel's `PostgresGrammar` to override `whereIn()`, `whereBasic()`, and `whereColumn()` with logic that detects `uuid` ↔ `varchar` comparisons and injects `::text` casts.

- Good, because it is a single registration point that catches all three failing query patterns
- Bad, because the Grammar layer has no schema metadata—detection requires heuristics (hardcoded table lists, column name conventions, or runtime schema introspection)
- Bad, because heuristic-based detection is fragile and error-prone
- Bad, because Grammar internals change across Laravel versions, creating maintenance burden
- Bad, because runtime schema introspection would introduce a performance penalty

## More Information

### Failing Query Patterns

Without the implicit cast, three categories of SQL fail in PostgreSQL 15:

1. **MorphTo eager loading**: `SELECT * FROM prospects WHERE id IN (?, ?)` — morph column values (varchar) compared to UUID primary key
2. **MorphMany lazy loading**: `WHERE respondent_id = ?` — UUID-typed parameter bound against varchar column
3. **whereHasMorph EXISTS subquery**: `WHERE prospects.id = cases.respondent_id` — UUID column compared to varchar column

### Scope of Changes

Only two models required primary key conversion:

- **Prospect**: Implements `Educatable` alongside `Student`; shares 15+ polymorphic relationships
- **CaseModel**: Has `respondent` morphTo relationship that resolves to `Student` or `Prospect`; also shares `interactable` morph via `HasManyMorphedInteractions`

All morph columns storing Student/Prospect/CaseModel IDs already use `varchar` type (created via `$table->morphs()`), so no morph columns needed type changes.

### References

- [PostgreSQL Type Conversion Documentation](https://www.postgresql.org/docs/15/typeconv.html)
- [Laravel Polymorphic Relationships](https://laravel.com/docs/eloquent-relationships#polymorphic-relationships)
