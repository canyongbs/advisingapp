---
title: Terms Consolidation
created: 2026-10-23
---

## Feature Flags

- App\Features\TermsConsolidationFeature

## Temporary Migrations

- app-modules/student-data-model/database/migrations/2026_10_23_195919_tmp_copy_enrollment_semesters_to_terms.php

## Additional Cleanup

- Search for `TODO: Cleanup Task (terms-consolidation)` and follow the instructions at each site.
- Add the `SoftDeletes` trait to the `Term` model; the `terms.deleted_at` column was added (without the trait) so the trait could not break queries before the migration ran.
- Drop the `enrollment_semesters` table and delete the `EnrollmentSemester` model, factory, policy, observer and resource.
- Delete or reassign the audits whose `auditable_type` is `enrollment_semester`, as the morph map is enforced and that model will no longer exist.

<!--
Only list cleanup that has no home in code. Do NOT restate obvious feature-flag
removals (keep the active path, drop the inactive path), and do NOT include file
paths or line numbers. For non-obvious changes, put a `TODO: Cleanup Task (<tag>)`
comment at the change site and reference the tag here — e.g.
"Search for `TODO: Cleanup Task (some-feature)`".
-->
