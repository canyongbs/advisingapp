---
title: Rename Surveys Duplicate Name
created: 2026-09-01
---

## Feature Flags

## Temporary Migrations

## Additional Cleanup

Once the migration has run in every environment, remove the one-off duplicate-renaming step
from `up()`. Delete only the `$this->fixDuplicates()` call and the `FixesDuplicateNames`
trait import — the surrounding schema changes (drop the old constraint, convert the column to
citext, create the partial unique index) are permanent and must stay.

- `app-modules/survey/database/migrations/2026_09_01_122143_convert_surveys_name_to_citext.php`

- If no other migration uses `FixesDuplicateNames` (`database/migrations/Concerns/FixesDuplicateNames.php`), restore its `// @phpstan-ignore trait.unused` annotation.
