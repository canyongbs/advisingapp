---
title: Customer Advisor Resource Hub Article Access Feature
created: 2026-08-17
---

## Feature Flags

- App\Features\CustomerAdvisorResourceHubArticleAccessFeature

## Temporary Migrations

## Additional Cleanup

### 1. `AdvisingApp\Ai\Models\CustomerAdvisor::getResourceHubArticles()`

File: `app-modules/ai/src/Models/CustomerAdvisor.php`

This method currently has an early-return fallback for when the flag is off (the old behaviour: any
customer advisor with `has_resource_hub_knowledge` gets every public article), followed by the new
access-level/category-aware query that only runs when the flag is on.

Delete the whole `if (! ... ::active())` block — keep only the query that follows it, unchanged:

Also remove the now-unused `use App\Features\CustomerAdvisorResourceHubArticleAccessFeature;` import from
the top of this file if nothing else in the class references it.

### 2. `AdvisingApp\Ai\Observers\CustomerAdvisorObserver::updated()`

File: `app-modules/ai/src/Observers/CustomerAdvisorObserver.php`

This method builds up a list of attributes that should trigger a vector-store re-upload when changed.
`resource_hub_article_access` is only added to that list when the flag is active.

Before:

```php
    $watchedAttributes = ['has_resource_hub_knowledge'];

    if (CustomerAdvisorResourceHubArticleAccessFeature::active()) {
        $watchedAttributes[] = 'resource_hub_article_access';
    }
```

After (put `resource_hub_article_access` directly in the array literal, matching how
`has_resource_hub_knowledge` is already declared — no conditional needed):

```php
public function updated(CustomerAdvisor $advisor): void
{
    $watchedAttributes = ['has_resource_hub_knowledge', 'resource_hub_article_access'];

    if ($advisor->wasChanged($watchedAttributes)) {
        UploadCustomerAdvisorFilesToVectorStore::dispatch($advisor);
    }
}
```

Also remove the now-unused `use App\Features\CustomerAdvisorResourceHubArticleAccessFeature;` import from
the top of this file if nothing else in the class references it.

### 3. `AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdvisorResourceHub::form()`

File: `app-modules/ai/src/Filament/Resources/CustomerAdvisors/Pages/ManageCustomerAdvisorResourceHub.php`

The two `Select` fields for article access and categories are only added to the schema array when the
flag is active, using an array spread over a ternary.

Drop the `...(... ::active() ? [...] : [])` spread/ternary entirely — the two `Select::make()`
fields become plain, unconditional entries in the `schema()` array, same as `Toggle::make()` above them:


### 4. `AdvisingApp\Ai\Filament\Resources\CustomerAdvisors\Pages\ManageCustomerAdvisorResourceHub::handleRecordUpdate()`

Same file as above. When an advisor's knowledge toggle is turned off, `resource_hub_article_access` is
only nulled out when the flag is active.

Drop the `... ::active() &&` half of the condition — keep only the
`! ($data['has_resource_hub_knowledge'] ?? false)` check:

Once both methods in this file no longer reference the flag, also remove the now-unused
`use App\Features\CustomerAdvisorResourceHubArticleAccessFeature;` import from the top of the file.

### 5. Migration: `create_customer_advisor_resource_hub_categories_table`

File: `app-modules/ai/database/migrations/2026_08_17_120100_create_customer_advisor_resource_hub_categories_table.php`

Remove only the `::activate()` / `::deactivate()` lines and the now-unused
`use App\Features\CustomerAdvisorResourceHubArticleAccessFeature;` import; Remove the `DB::transaction()`
wrapper as well:


Finally, once all 4 code sites above no longer reference the flag, delete the flag class itself
(`app/Features/CustomerAdvisorResourceHubArticleAccessFeature.php`) and this cleanup task file.
