---
title: Form Versioning Feature
created: 2026-06-15
---

## Feature Flags

- App\Features\FormVersioningFeature

## Temporary Migrations

- The feature flag activation in `app-modules/application/database/migrations/2026_06_15_143001_add_versioning_to_applications_table.php` (the `FormVersioningFeature::activate()` / `deactivate()` calls) can be removed once the flag is cleaned up

## Additional Cleanup

- Search for `TODO: Cleanup Task - FormVersioningFeature` in the codebase for inline cleanup instructions (migration backfill comments)

- In `app-modules/form/src/Filament/Resources/Forms/Pages/EditForm.php`: remove the `FormVersioningFeature::active()` guard and the `parent::save()` fallback — the versioning save path should be the only path

- In `app-modules/application/src/Filament/Resources/Applications/Pages/EditApplication.php`: same as above

- In `app-modules/form/src/Filament/Resources/Forms/Pages/Concerns/HasSharedFormConfiguration.php`: remove the `! FormVersioningFeature::active() &&` conditions from the three `disabled()` calls on `is_wizard`, the Fields section, and the steps Repeater — they should never be disabled based on submission count. Also remove the `FormVersioningFeature::active() ?` ternary in the `unique()` `modifyRuleUsing` — just use `$rule->whereNull('archived_at')` directly.

- In `app-modules/application/src/Filament/Resources/Applications/Pages/Concerns/HasSharedFormConfiguration.php`: same as above

- In `app-modules/form/src/Filament/Resources/Forms/FormResource.php`: remove the `FormVersioningFeature::active()` guard — `withoutArchived()` should always be applied

- In `app-modules/application/src/Filament/Resources/Applications/ApplicationResource.php`: same as above

- In `app-modules/form/src/Http/Controllers/FormWidgetController.php`: remove the `FormVersioningFeature::active()` early return in `resolveToLatestVersion()` — the versioning resolution should always run

- In `app-modules/application/src/Http/Controllers/ApplicationWidgetController.php`: same as above

- In `app-modules/form/src/Jobs/SendFormNotificationJob.php`: remove the `FormVersioningFeature::active()` ternary — always resolve latest version

- In `app-modules/application/src/Jobs/SendApplicationNotificationJob.php`: same as above

- In `app-modules/form/src/Listeners/TriggerFormSubmissionWorkflows.php`: remove the `FormVersioningFeature::active()` guard — always resolve latest version

- In `app-modules/application/src/Listeners/TriggerApplicationSubmissionStageWorkflows.php`: same as above

- In `app-modules/form/src/Listeners/SendFormSubmissionAutoReplyEmailToSubmitter.php`: remove the `FormVersioningFeature::active()` guard — always resolve latest version

- In `app-modules/form/src/Notifications/FormSubmissionAutoReplyNotification.php`: same as above

- In `app-modules/form/src/Filament/Resources/Forms/Pages/ManageFormSubmissions.php`: remove the `FormVersioningFeature::active()` ternaries in `table()`, `headerActions`, and `getNavigationItems()` — the cross-version query using `root_id` should be the only path

- In `app-modules/application/src/Filament/Resources/Applications/Pages/ManageApplicationSubmissions.php`: same as above

- In `app-modules/form/src/Observers/FormObserver.php`: remove the `FormVersioningFeature::active()` guard — always set `root_id`

- In `app-modules/application/src/Observers/ApplicationObserver.php`: same as above

- Delete the feature flag class: `app/Features/FormVersioningFeature.php`
