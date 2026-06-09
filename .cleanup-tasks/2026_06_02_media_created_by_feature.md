---
title: Media Created By Feature
created: 2026-06-02
---

## Feature Flags

- App\Features\MediaCreatedByFeature

## Temporary Migrations

## Additional Cleanup

- In `database/migrations/2026_06_02_214123_add_created_by_to_media_table.php`:
    1. Remove the `MediaCreatedByFeature::activate()` call from the `up()` method
    2. Remove the `MediaCreatedByFeature::deactivate()` call from the `down()` method
    3. Remove the `use App\Features\MediaCreatedByFeature;` import

- In `app/Models/Media.php`: in `getCreatedByNameAttribute()` and `getCreatedBySubLabelAttribute()`, remove the `! MediaCreatedByFeature::active() ||` guard conditions — the methods should always resolve the creator. Remove the `use App\Features\MediaCreatedByFeature;` import.

- In `app/Observers/MediaObserver.php`: in `creating()`, remove the `! MediaCreatedByFeature::active() ||` condition from the early-return guard — the observer should always associate the authenticated user as creator. Remove the `use App\Features\MediaCreatedByFeature;` import.

- In `app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php`: remove all four `if (MediaCreatedByFeature::active() && is_null($media->created_by_id))` blocks — always associate the creator unconditionally (keep the `$media->createdBy()->associate(...)` and `$media->saveQuietly()` calls directly). Remove the `use App\Features\MediaCreatedByFeature;` import.

- In `app-modules/authorization/src/Http/Controllers/SocialiteController.php`: remove the `if (MediaCreatedByFeature::active() && is_null($media->created_by_id))` guard — always associate `$user` as the media creator unconditionally. Remove the `use App\Features\MediaCreatedByFeature;` import.

- In `app-modules/form/src/Actions/ProcessSubmissionField.php`: remove all `if (MediaCreatedByFeature::active() && is_null($media->created_by_id))` guards — always associate the author/creator unconditionally. Remove the `use App\Features\MediaCreatedByFeature;` import.

- Remove every remaining `use App\Features\MediaCreatedByFeature;` import that becomes unused after the above edits.

- Delete the feature flag class itself: `app/Features/MediaCreatedByFeature.php`.
