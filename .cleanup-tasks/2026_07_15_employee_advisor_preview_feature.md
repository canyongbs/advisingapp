---
title: Employee Advisor Preview Feature
created: 2026-07-15
---

## Feature Flags

- App\Features\EmployeeAdvisorPreviewFeature

## Temporary Migrations

- `app-modules/ai/database/migrations/2026_07_15_110053_data_activate_employee_advisor_preview_feature.php`

## Additional Cleanup

- Remove `->when` and featureflag condition in `app-modules/ai/src/Filament/Pages/Assistant/Concerns/CanManageFolders.php` file.

- Remove `->when` and featureflag condition in `app-modules/ai/src/Filament/Pages/Assistant/Concerns/CanManageThreads.php` file.

- Remove feature flag condition in `app-modules/ai/src/Filament/Resources/AiAssistants/Pages/PreviewEmployeeAdvisor.php` at `canaccess` and `mount` function
