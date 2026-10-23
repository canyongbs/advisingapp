---
paths:
    - 'app-modules/student-data-model/**'
---

# Student Data Model

## Enrollment-to-term matching lives in the enrollment_terms view

`Enrollment::term()` and `Term::enrollments()` go through the `enrollment_terms` Postgres view, which holds the single enrollment-to-term matching rule (by `sis_term_id`, otherwise `semester_name` ignoring case, preferring SIS terms then the latest start date, ignoring soft-deleted terms). Change the rule only with `CREATE OR REPLACE VIEW` in a migration. Postgres blocks dropping or changing the type of any column the view uses (`enrollments.id`, `sis_term_id`, `semester_name`; `terms.id`, `sis_term_id`, `name`, `start_date`, `deleted_at`) and dropping either table, so drop the view first and recreate it in the same migration. Avoid whole-table queries in the term-to-enrollments direction: the view cannot use an index from the term side.
