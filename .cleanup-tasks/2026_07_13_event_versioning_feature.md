---
title: Event Versioning Feature
created: 2026-07-13
---

## Feature Flags

- App\Features\EventVersioningFeature

## Temporary Migrations

## Additional Cleanup

- In `app-modules/meeting-center/database/migrations/2026_07_13_115355_add_versioning_to_event_registration_forms_table.php`:
    1. Remove the backfill statement and its TODO comment:
        ```php
        // TODO: Cleanup Task - EventVersioningFeature - This backfill can be removed once all environments have run this migration
        DB::update('UPDATE event_registration_forms SET root_id = id WHERE root_id IS NULL');
        ```
    2. Remove the feature flag activate and deactivate from `up()` and `down()` method respectively.

- In `app-modules/meeting-center/src/Models/Event.php`:
    1. Replace the guarded `eventRegistrationForm()` relationship with an unconditional version — keep `->whereNull('archived_at')` but remove the `if (EventVersioningFeature::active())` wrapper:
        ```php
        // Before
        if (EventVersioningFeature::active()) {
            $relationship->whereNull('archived_at');
        }
        // After — remove the if, keep the whereNull
        $relationship->whereNull('archived_at');
        ```
    2. Remove the `use App\Features\EventVersioningFeature;` import.

- In `app-modules/meeting-center/src/Observers/EventRegistrationFormObserver.php`:
    1. Remove the `if (! EventVersioningFeature::active()) { return; }` early-return guard — let `$form->root_id = $form->id` run unconditionally.
    2. Remove the `use App\Features\EventVersioningFeature;` import.

- In `app-modules/meeting-center/src/Filament/Resources/Events/Pages/EditEventRegistration.php`:
    1. In `saveRelationshipsBeforeChildrenUsing`, remove the `if (EventVersioningFeature::active()) { ... } else { $record->fill($data); $record->save(); }` branching — keep only the versioning branch (the `if` body) and un-indent it.
    2. Remove the `! EventVersioningFeature::active()` part form the three disabled modifiers.
    3. Remove the `use App\Features\EventVersioningFeature;` import.

- In `app-modules/meeting-center/src/Filament/Resources/Events/Pages/Concerns/HasSharedEventFormConfiguration.php`:
    1. Remove the `! EventVersioningFeature::active()` part form the three disabled modifiers.
    2. Remove the `use App\Features\EventVersioningFeature;` import.

- In `app-modules/meeting-center/src/Actions/DuplicateEvent.php`
    1. Remove the condition `if (EventVersioningFeature::active())` and the code inside it should run unconditionally.

- Delete the feature flag class itself: `app/Features/EventVersioningFeature.php`.
