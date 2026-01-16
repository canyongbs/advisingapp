---
status: 'proposed'
date: 2026-01-16
decision-makers: Kevin Ullyott, Dan Harrin, Payal Baldaniya
consulted: Kevin Ullyott, Dan Harrin, Payal Baldaniya
---

# How to Ensure Case-insensitive String Comparision in Postgres

## Context and Problem Statement

By default in Postgres, strings are compared case-sensitive, meaning "ABC" != "abc".

This is problematic when we NEED some comparisons to be case-insensitive. For example, if we need email addresses in the system to be unique and we want to consider "Example@email.com" to violate "example@email.com" in a unique index.

Though there may be areas where this is managed ad hoc, there is a desire for there to be a general strategy for enabling this functionality

## Considered Options

- citext data type
- Functional (Expression) Index
- Manual handling in code/queries

## Decision Outcome

Chosen option: "citext data type", because it provides transparent case-insensitive comparison at the database level without requiring application-level handling, works seamlessly with existing queries and unique constraints, and is a well-supported PostgreSQL extension.

### Consequences

- Good, because case-insensitive uniqueness is enforced at the database level, preventing duplicates regardless of how data is inserted
- Good, because existing queries work without modification—no need to wrap comparisons in `LOWER()` or `ILIKE`
- Good, because it integrates naturally with Laravel's Eloquent ORM and unique validation rules
- Good, because it reduces cognitive overhead for developers who don't need to remember to handle case sensitivity manually
- Bad, because it requires the `citext` extension to be enabled in PostgreSQL (`CREATE EXTENSION citext`)
- Neutral, because there is a minor performance overhead compared to standard `text`, but it is negligible for most use cases

### Confirmation

Compliance with this ADR can be confirmed through:

1. **Database schema review**: Verify that columns requiring case-insensitive comparison (e.g., email addresses) use the `citext` data type
2. **Migration audits**: Ensure new migrations for case-insensitive columns specify `citext` rather than `string` or `text`
3. **Integration tests**: Write tests that attempt to insert duplicate values with different casing to confirm uniqueness constraints work correctly

## Pros and Cons of the Options

### citext data type

The `citext` extension provides a case-insensitive text data type. Internally, it stores text as-is but performs case-insensitive comparisons using `LOWER()` transparently.

```sql
CREATE EXTENSION citext;
ALTER TABLE users ALTER COLUMN email TYPE citext;
```

- Good, because comparison is handled transparently at the database level
- Good, because unique constraints automatically enforce case-insensitive uniqueness
- Good, because no changes needed to application queries or validation logic
- Good, because the original case is preserved in storage (e.g., "Example@Email.com" is stored as entered)
- Neutral, because it requires enabling a PostgreSQL extension (but `citext` is a trusted, built-in extension)
- Bad, because it is PostgreSQL-specific and reduces portability to other databases
- Bad, because there is a slight performance cost compared to `text` (though typically negligible)

### Functional (Expression) Index

Create a unique index on a lowercase expression of the column to enforce case-insensitive uniqueness.

```sql
CREATE UNIQUE INDEX users_email_unique ON users (LOWER(email));
```

- Good, because it doesn't require any extension
- Good, because the column remains standard `text` or `varchar`
- Good, because it works well for uniqueness enforcement
- Bad, because queries must explicitly use `LOWER()` to leverage the index (e.g., `WHERE LOWER(email) = LOWER(?)`)
- Bad, because developers must remember to apply `LOWER()` consistently in application code
- Bad, because Laravel's built-in unique validation rules don't automatically use the functional index
- Bad, because it's easy to accidentally bypass the case-insensitive logic

### Manual handling in code/queries

Handle case-insensitivity entirely in application code by normalizing values before storage and using `LOWER()` or `ILIKE` in queries.

```php
$user->email = strtolower($request->email);
```

- Good, because it doesn't require any database-specific features
- Good, because it provides maximum control over normalization logic
- Good, because it is portable across different database systems
- Bad, because it requires consistent discipline across the entire codebase
- Bad, because it's error-prone—easy to forget normalization in some code paths
- Bad, because it doesn't protect against direct database inserts that bypass application logic
- Bad, because existing data may need migration to normalize case
- Bad, because the original casing entered by the user is lost

## More Information

### Implementation Notes

1. The `citext` extension should be enabled in a database migration:

    ```php
    DB::statement('CREATE EXTENSION IF NOT EXISTS citext');
    ```

2. For Laravel migrations, use raw SQL or a custom column type to define `citext` columns:

    ```php
    $table->addColumn('citext', 'email');
    ```

3. Consider creating a reusable migration helper or custom Blueprint macro for `citext` columns.

### References

- [PostgreSQL citext documentation](https://www.postgresql.org/docs/current/citext.html)
- [PostgreSQL Functional Indexes](https://www.postgresql.org/docs/current/indexes-expressional.html)
