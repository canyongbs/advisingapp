---
title: Subscription Expiration Feature
created: 2026-07-08
---

## Feature Flags

- App\Features\SubscriptionExpirationFeature

## Temporary Migrations

- `database/landlord/2026_07_08_132459_data_activate_subscription_expiration_feature.php`

## Additional Cleanup

Once `SubscriptionExpirationFeature` is fully rolled out and the flag is removed, search the codebase for `TODO: Cleanup Task` and apply the following changes:

- In `app/Http/Controllers/Tenants/SyncTenantController.php`:
    1. Remove the `if (SubscriptionExpirationFeature::active()) { ... }` wrapper around the `subscription_status` / `expirationBannerText` writes — keep the two inner `if (filled(...))` blocks running unconditionally.
    2. Remove the `use App\Features\SubscriptionExpirationFeature;` import.

- In `app/Multitenancy/TenantFinder/SubscriptionAwareDomainTenantFinder.php`:
    1. Remove the `if (! SubscriptionExpirationFeature::active()) { return parent::findForRequest($request); }` early-return guard — keep only the subscription-aware query.
    2. Remove the `use App\Features\SubscriptionExpirationFeature;` import.

- In `app/Providers/Filament/AdminPanelProvider.php`:
    1. Remove the `if (! SubscriptionExpirationFeature::active()) { return null; }` guard from the `TOPBAR_AFTER` render hook — keep the `subscription_status?->showsExpirationBanner()` check.
    2. Remove the `use App\Features\SubscriptionExpirationFeature;` import.

- Delete the feature flag class itself: `app/Features/SubscriptionExpirationFeature.php`.

- Delete the activation migration listed under Temporary Migrations above.
