---
paths:
    - 'app-modules/student-data-model/**'
---

# Student Data Model

## Enrollment-to-term matching lives in the enrollment_terms view

`Enrollment::term()` and `Term::enrollments()` go through the `enrollment_terms` Postgres view, which holds the single enrollment-to-term matching rule (by `sis_term_id`, otherwise `semester_name` ignoring case, preferring SIS terms then the latest start date, ignoring soft-deleted terms). Change the rule only with `CREATE OR REPLACE VIEW` in a migration. Postgres blocks dropping or changing the type of any column the view uses (`enrollments.id`, `sis_term_id`, `semester_name`; `terms.id`, `sis_term_id`, `name`, `start_date`, `deleted_at`) and dropping either table, so drop the view first and recreate it in the same migration. Avoid whole-table queries in the term-to-enrollments direction: the view cannot use an index from the term side.

## Filter many enrollments by term with the MatchedToTermNamed / WithoutMatchedTerm scopes

Resolving terms through `enrollment_terms` costs a per-enrollment lookup, so `whereHas('term')` / `whereDoesntHave('term')` across a whole tenant (e.g. the students list enrollments filter) took 20-40s on 400k enrollments. Use `MatchedToTermNamed` (narrows to candidates by `sis_term_id` / `semester_name` before checking the view) and `WithoutMatchedTerm` (the view's candidate conditions negated, no view) instead; both took under 1.5s. When the view's matching rule changes, update these scopes too; their tests compare them against the view.
