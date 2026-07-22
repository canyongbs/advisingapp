---
title: Reporting Feature
created: 2026-07-01
---

## Feature Flags

- App\Features\ReportingFeature

## Temporary Migrations

- `app-modules/report/database/migrations/2026_07_01_122918_data_activate_reporting_feature.php`

## Additional Cleanup

Once `ReportingFeature` is fully rolled out and the flag is removed, the following cleanups are required:

### 1. Delete the feature flag class

- Delete `app/Features/ReportingFeature.php`

### 2. Simplify `canAccess()` in all abstract report base classes

Each of the following classes has a `ReportingFeature::active()` branch that falls back to the old `$user->can('report-library.view-any')` / `$user->can('reporting.view-any')` permission check. Remove the `if (! ReportingFeature::active())` guard and keep only the new `ReportAccess::userCanAccessPage()` branch:

- `app-modules/report/src/Abstract/AiReport.php`
- `app-modules/report/src/Abstract/EngagementReport.php`
- `app-modules/report/src/Abstract/ProspectReport.php`
- `app-modules/report/src/Abstract/StudentReport.php`
- `app-modules/report/src/Abstract/UserReport.php`

Example — before:

```php
if (! ReportingFeature::active()) {
    return $user->hasLicense(LicenseType::RetentionCrm) && $user->can('report-library.view-any');
}

return $user->hasLicense(LicenseType::RetentionCrm) && ReportAccess::userCanAccessPage(static::class, $user);
```

After:

```php
return $user->hasLicense(LicenseType::RetentionCrm) && ReportAccess::userCanAccessPage(static::class, $user);
```

### 3. Simplify `canAccess()` on the Reporting management page

- `app-modules/authorization/src/Filament/Pages/Reporting.php`

Remove the `ReportingFeature::active()` guard from `canAccess()` and keep only the permission check required for the management page itself.

### 4. Remove `ReportingFeature::activate()` from all tests

All tests that call `ReportingFeature::activate()` in their `beforeEach` / setup should have that call removed once the flag defaults to `true`:

- `app-modules/authorization/tests/Tenant/Filament/Pages/ReportingTest.php`
- `app-modules/prospect/tests/Tenant/Unit/RecruitmentCrmDashboardTest.php`
- `app-modules/student-data-model/tests/Tenant/Unit/RetentionCrmDashboardTest.php`
- All files under `app-modules/report/tests/` that call `ReportingFeature::activate()`

### 5. Remove the old `reporting.view-any` / `report-library.view-any` permission guards

Once the flag is removed and `ReportAccess::userCanAccessPage()` is the sole gate, audit whether the `reporting.view-any` permission (used by the Reporting management page) and `report-library.view-any` permission (old report access gate) are still needed or can be deprecated.
