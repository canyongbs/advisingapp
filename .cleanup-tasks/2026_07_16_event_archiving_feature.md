---
title: Event Archiving Feature
created: 2026-07-16
---

## Feature Flags

- App\Features\EventArchivingFeature

## Temporary Migrations

## Additional Cleanup

- In `app-modules/meeting-center/src/Filament/Resources/Events/Pages/EditEventDetails.php`:
    1. Replace the ternary `return EventArchivingFeature::active() ? [ ... ] : [ ... ];` in `getHeaderActions()` with the unconditional version — keep only the `ArchiveAction::make()` and `DeleteAction::make()->hidden(...)` array, dropping the ternary wrapper entirely.

- In `app-modules/meeting-center/src/Filament/Resources/Events/Pages/ListEvents.php`:
    1. In `toolbarActions()`, replace the spread ternary `...(EventArchivingFeature::active() ? [ ArchiveBulkAction::make('') ... ] : [DeleteBulkAction::make()...])` with just the unconditional `ArchiveBulkAction::make('')` action (dropping the ternary, the spread, and the `DeleteBulkAction` fallback).
    2. Remove the `use Filament\Actions\DeleteBulkAction;` import (it is only used in the feature-flagged fallback).

- In `app-modules/meeting-center/database/migrations/2026_07_16_162334_add_archived_at_to_events_table.php` remove the `EventArchivingFeature::activate()` call from `up()` and the `EventArchivingFeature::deactivate()` call from `down()`, and remove the `use App\Features\EventArchivingFeature;` import. The `DB::transaction()` wrapper should also be removed.

- Delete the feature flag class itself: `app/Features/EventArchivingFeature.php`.
