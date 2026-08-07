---
title: Rename Customer And Employee Categories Duplicate Name
created: 2026-08-05
---

## Feature Flags

## Temporary Migrations

## Additional Cleanup

- In `app-modules/ai/database/migrations/2026_08_05_175003_convert_customer_advisor_categories_name_to_citext.php` From the up() method remove `$this->fixCaseInsensitiveDuplicateNames()`.

- In `app-modules/ai/database/migrations/2026_08_05_175321_convert_employee_advisor_category_names_to_citext.php` From the up() method remove `$this->fixCaseInsensitiveDuplicateNames()`.
