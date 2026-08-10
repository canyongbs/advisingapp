---
title: Rename Customer And Employee Categories Duplicate Name
created: 2026-08-05
---

## Feature Flags

## Temporary Migrations

## Additional Cleanup

Once both migrations have run in every environment, remove the one-off duplicate-renaming step
from each `up()`. Delete only the `$this->fixDuplicates()` call and the `FixesDuplicateNames`
trait import — the surrounding schema changes (drop the old constraint, convert the column to
citext, create the partial unique index) are permanent and must stay.

- `app-modules/ai/database/migrations/2026_08_05_175003_convert_customer_advisor_categories_name_to_citext.php`
- `app-modules/ai/database/migrations/2026_08_05_175321_convert_employee_advisor_category_names_to_citext.php`
