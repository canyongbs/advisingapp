---
title: Subscription Expiration Feature
created: 2026-07-08
---

## Feature Flags

- App\Features\SubscriptionExpirationFeature

## Temporary Migrations

- `database/landlord/2026_07_08_132459_data_activate_subscription_expiration_feature.php`

## Additional Cleanup

- In `app/Http/Requests/Tenants/SyncTenantRequest.php`:
    1. Change the `subscriptionStatus` rule from `['nullable', Rule::enum(SubscriptionStatus::class)]` back to `['required', Rule::enum(SubscriptionStatus::class)]` — safe once Olympus always sends the field.

- In `app/Http/Controllers/Tenants/SyncTenantController.php`:
    1. Remove the `if (SubscriptionExpirationFeature::active()) { ... }` wrapper — keep the `DB::connection('landlord')->transaction(...)` block (with its two inner `if (filled(...))` writes) running unconditionally.
    2. Remove only the `use App\Features\SubscriptionExpirationFeature;` import. Leave the try/catch, both connection transactions, and the `DB` / `Throwable` imports in place — they are permanent, not part of the feature flag.

- In `app/Multitenancy/TenantFinder/SubscriptionAwareDomainTenantFinder.php`:
    1. Remove the `if (! SubscriptionExpirationFeature::active()) { return parent::findForRequest($request); }` early-return guard — keep only the subscription-aware query.
    2. Remove the `use App\Features\SubscriptionExpirationFeature;` import.

- In `app/Providers/Filament/AdminPanelProvider.php`:
    1. Remove the `if (! SubscriptionExpirationFeature::active()) { return null; }` guard from the `TOPBAR_AFTER` render hook — keep the `subscription_status?->showsExpirationBanner()` check.
    2. Remove the `use App\Features\SubscriptionExpirationFeature;` import.

- Delete the feature flag class itself: `app/Features/SubscriptionExpirationFeature.php`.

- Delete the activation migration listed under Temporary Migrations above.
