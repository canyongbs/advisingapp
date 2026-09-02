---
title: Student Archiving Feature
created: 2026-08-31
---

## Feature Flags

- App\Features\StudentArchivingFeature

## Temporary Migrations

## Additional Cleanup

- In `UtilizationMetricsApiController::__invoke()`, the alert counts are built with a `->when(StudentArchivingFeature::active(), ...)` wrapper because raw SQL cannot use the `WithoutArchivedStudents` scope. Drop the `when()` wrapper and chain its `leftJoin('students', ...)` and `where(...)` directly onto the `AlertConfiguration` query, then remove the `use App\Features\StudentArchivingFeature;` import.

- In `EducatableSelect::getStudentType()`, delete the `TODO: Cleanup Task` comment block and the `if (! StudentArchivingFeature::active()) { return; }` guard beneath it from the `modifyOptionsQueryUsing()` closure, then remove the `use App\Features\StudentArchivingFeature;` import. Leave everything from `$query->where(...)` onward untouched, including its comment — the `where()` / `tap()` / `when()` / `orWhere()` chain does not change. The guard only exists because the scope is a no-op while the feature is inactive, which would leave the `orWhere` as the group's only condition; once the scope always applies `withoutArchived()` the chain is correct on its own.

- In `EditInteractionTest`, delete the `it('still offers other students while the feature is inactive')` test — it only covers the guard above. Keep the other two tests in the `archived students` block.

- Keep the `WithoutArchivedStudents` scope and every `->tap(new WithoutArchivedStudents())` call site. Only the `StudentArchivingFeature::active()` guard inside the scope is removed, so it always applies `withoutArchived()`. Do not inline the scope into its call sites.

- In `WithoutArchivedStudentsTest`, delete the `it('leaves the query untouched while the feature is inactive')` test — it only covers the flag-inactive branch of the scope, which no longer exists once the guard is removed. Keep `it('excludes archived students')`, and remove the `use App\Features\StudentArchivingFeature;` import that becomes unused. This is the only test in the suite that calls `StudentArchivingFeature::deactivate()`.

- In `HasStudentHeader::getHeaderActions()`, `EditStudent::getHeaderActions()` and `ListStudents::table()`, keep only the archive branch of each ternary and delete the `DeleteAction` / `DeleteBulkAction` fallbacks, along with the imports that become unused. `DeleteStudent` itself stays — the V1 API delete controller still uses it.

- In the `add_archived_at_to_students_table` migration, remove the `StudentArchivingFeature::activate()` call from `up()` and the `deactivate()` call from `down()`, remove the `use App\Features\StudentArchivingFeature;` import, and drop the `DB::transaction()` wrappers.

- Delete the feature flag class itself: `app/Features/StudentArchivingFeature.php`.
