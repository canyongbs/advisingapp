---
paths:
    - 'app-modules/student-data-model/**'
    - 'app-modules/alert/**'
---

# Student Data Model

## Enrollment-to-term matching lives in the enrollment_terms view

`Enrollment::term()` and `Term::enrollments()` go through the `enrollment_terms` Postgres view, which holds the single enrollment-to-term matching rule (by `sis_term_id`, otherwise `semester_name` ignoring case, preferring SIS terms then the latest start date, ignoring soft-deleted terms). Everything that resolves an enrollment's term (the enrollments list, First/Most Recent Enrolled Term, the New Student alert) uses it, so they agree; don't add another rule (e.g. comparing `terms.code` with `semester_code`) in one place. Change the rule only with `CREATE OR REPLACE VIEW` in a migration. Postgres blocks dropping or changing the type of any column the view uses (`enrollments.id`, `sis_term_id`, `semester_name`; `terms.id`, `sis_term_id`, `name`, `start_date`, `deleted_at`) and dropping either table, so drop the view first and recreate it in the same migration. Avoid whole-table queries in the term-to-enrollments direction: the view cannot use an index from the term side.

## Join enrollments to terms; don't resolve them per row across a tenant

Joining through the view is fast across a whole tenant, as Postgres caches its lookup per distinct SIS term ID and semester name (~1s on 600k enrollments): use `JoinMatchedTerm`, with `OrderByFirstTerm` / `OrderByMostRecentTerm`, and `DISTINCT ON` to pick one term per student. A correlated per-row lookup (`whereHas('term')`, `whereDoesntHave('term')`, or a per-student subquery evaluated for every student) took 17-40s; keep those to a page of students (e.g. eager loading `firstTermEnrollment`). To filter by term names use `MatchedToTermNamed` / `WithoutMatchedTerm`. When the view's matching rule changes, update these scopes too; their tests compare them against the view.

## Views depend on the student data tables

Besides `enrollment_terms`, the per-tenant `student_alerts` view is generated from the enabled alerts' queries (`GenerateStudentAlertsView`), so it can depend on `students`, `enrollments`, `enrollment_terms` and anything else an alert reads. Code that drops or replaces these tables (e.g. the student data import's table swap) must run inside `App\Actions\RecreateDependentViews`, which recreates every dependent view. A migration that drops or changes a column an alert reads must drop `student_alerts` first and run `GenerateStudentAlertsView` after. A change to an alert's query only reaches a tenant when its view is regenerated, so do that in the migration that activates the change.
