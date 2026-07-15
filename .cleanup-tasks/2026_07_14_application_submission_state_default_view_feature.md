---
title: Application Submission State Default View Feature
created: 2026-07-14
---

## Feature Flags

- App\Features\ApplicationSubmissionStateDefaultViewFeature

## Temporary Migrations

_(none)_

## Additional Cleanup

- In `app-modules/application/src/Filament/Resources/Applications/Pages/ManageApplicationSubmissions.php`:
    1. Remove the `if (! ApplicationSubmissionStateDefaultViewFeature::active()) { ... }` fallback block — keep only the `is_default` query and the `return` statement that follows it.
    2. Remove the `use App\Features\ApplicationSubmissionStateDefaultViewFeature;` import.

- In `app-modules/application/src/Filament/Resources/ApplicationSubmissionStates/Pages/CreateApplicationSubmissionState.php`:
    1. Remove `->visible(fn () => ApplicationSubmissionStateDefaultViewFeature::active())` from `Toggle::make('is_default')`.
    2. Remove the `use App\Features\ApplicationSubmissionStateDefaultViewFeature;` import.

- In `app-modules/application/src/Filament/Resources/ApplicationSubmissionStates/Pages/EditApplicationSubmissionState.php`:
    1. Remove `->visible(fn () => ApplicationSubmissionStateDefaultViewFeature::active())` from `Toggle::make('is_default')`.
    2. Remove the `use App\Features\ApplicationSubmissionStateDefaultViewFeature;` import.

- In `app-modules/application/src/Filament/Resources/ApplicationSubmissionStates/Pages/ListApplicationSubmissionStates.php`:
    1. Remove `->visible(fn () => ApplicationSubmissionStateDefaultViewFeature::active())` from `IconColumn::make('is_default')`.
    2. Remove the `use App\Features\ApplicationSubmissionStateDefaultViewFeature;` import.

- In `app-modules/application/src/Filament/Resources/ApplicationSubmissionStates/Pages/ViewApplicationSubmissionState.php`:
    1. Remove `->visible(fn () => ApplicationSubmissionStateDefaultViewFeature::active())` from `IconEntry::make('is_default')`.
    2. Remove the `use App\Features\ApplicationSubmissionStateDefaultViewFeature;` import.

- Delete the feature flag class itself: `app/Features/ApplicationSubmissionStateDefaultViewFeature.php`.
