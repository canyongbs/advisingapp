---
title: Archive Submissions Feature
created: 2026-07-14
---

## Feature Flags

- App\Features\ArchiveSubmissionsFeature

## Temporary Migrations

- `database/migrations/2026_07_14_143325_tmp_activate_archive_submissions_feature_flag.php`

## Additional Cleanup

- In `app-modules/form/src/Filament/Resources/Forms/Pages/ManageFormSubmissions.php`:
    1. Remove the `...(ArchiveSubmissionsFeature::active() ? [ Filter::make('withoutArchived') ... ] : [])` spread in `->filters()` — keep only the `FormSubmissionStatusFilter` and `SelectFilter` for author type; the `withoutArchived` filter becomes unconditional.
    2. In `->recordActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ Action::make('archive') ... ] : [ DeleteAction::make() ])` spread — keep the `archive` action unconditionally (drop the `else` branch with `DeleteAction`).
    3. In `->toolbarActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ BulkAction::make('archive') ... ] : [ DeleteBulkAction::make() ])` spread — keep the `archive` bulk action unconditionally (drop the `else` branch with `DeleteBulkAction`).
    4. In `getNavigationItems()`, remove the `->when(ArchiveSubmissionsFeature::active(), fn (Builder $query) => $query->withoutArchived())` call — apply `->withoutArchived()` unconditionally on the nav count query.
    5. Remove the `use App\Features\ArchiveSubmissionsFeature;` import.

- In `app-modules/application/src/Filament/Resources/Applications/Pages/ManageApplicationSubmissions.php`:
    1. Remove the `...(ArchiveSubmissionsFeature::active() ? [ Filter::make('withoutArchived') ... ] : [])` spread in `->filters()` — keep the `withoutArchived` filter unconditionally.
    2. In `->recordActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ Action::make('archive') ... ] : [ DeleteAction::make() ])` spread — keep the `archive` action unconditionally (drop the `else` branch with `DeleteAction`).
    3. In `->toolbarActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ BulkAction::make('archive') ... ] : [ DeleteBulkAction::make() ])` spread — keep the `archive` bulk action unconditionally (drop the `else` branch with `DeleteBulkAction`).
    4. In `getNavigationItems()`, remove the `->when(ArchiveSubmissionsFeature::active(), fn (Builder $query) => $query->withoutArchived())` call — apply `->withoutArchived()` unconditionally on the nav count query.
    5. Remove the `use App\Features\ArchiveSubmissionsFeature;` import.

- In `app-modules/meeting-center/src/Filament/Resources/Events/Pages/ManageEventAttendees.php`:
    1. Remove the `...(ArchiveSubmissionsFeature::active() ? [ Filter::make('withoutArchived') ... ] : [])` spread in `->filters()` — keep the `withoutArchived` filter unconditionally.
    2. In `->recordActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ Action::make('archive') ... ] : [])` spread — keep the `archive` action unconditionally (drop the empty `else` branch).
    3. In `->toolbarActions()`, remove the `...(ArchiveSubmissionsFeature::active() ? [ BulkAction::make('archive') ... ] : [])` spread — keep the `archive` bulk action unconditionally (drop the empty `else` branch).
    4. Remove the `use App\Features\ArchiveSubmissionsFeature;` import.

- Delete the feature flag class itself: `app/Features/ArchiveSubmissionsFeature.php`.
