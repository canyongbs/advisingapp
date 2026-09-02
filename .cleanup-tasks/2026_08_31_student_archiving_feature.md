---
title: Student Archiving Feature
created: 2026-08-31
---

## Feature Flags

- App\Features\StudentArchivingFeature

## Temporary Migrations

## Additional Cleanup

- In `UtilizationMetricsApiController::__invoke()`, the alert counts are built with a `->when(StudentArchivingFeature::active(), ...)` wrapper because raw SQL cannot use the `WithoutArchivedStudents` scope. Drop the `when()` wrapper and chain its `leftJoin('students', ...)` and `where(...)` directly onto the `AlertConfiguration` query, then remove the `use App\Features\StudentArchivingFeature;` import.

- Keep the `WithoutArchivedStudents` scope and every `->tap(new WithoutArchivedStudents())` call site. Only the `StudentArchivingFeature::active()` guard inside the scope is removed, so it always applies `withoutArchived()`. Do not inline the scope into its call sites.

- In `HasStudentHeader::getHeaderActions()`, `EditStudent::getHeaderActions()` and `ListStudents::table()`, keep only the archive branch of each ternary and delete the `DeleteAction` / `DeleteBulkAction` fallbacks, along with the imports that become unused. `DeleteStudent` itself stays — the V1 API delete controller still uses it.

- In the `add_archived_at_to_students_table` migration, remove the `StudentArchivingFeature::activate()` call from `up()` and the `deactivate()` call from `down()`, remove the `use App\Features\StudentArchivingFeature;` import, and drop the `DB::transaction()` wrappers.

- Delete the feature flag class itself: `app/Features/StudentArchivingFeature.php`.
