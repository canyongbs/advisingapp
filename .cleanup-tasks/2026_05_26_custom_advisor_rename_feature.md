---
title: Custom Advisor Rename Feature
created: 2026-05-26
---

## Feature Flags

- App\Features\CustomAdvisorRenameFeature

## Temporary Migrations

## Additional Cleanup

- Search for `TODO: CustomAdvisorRenameFeature clean up` in the codebase — every gated branch has an inline note explaining exactly what to keep and what to delete.
- Search for `CustomAdvisorRenameFeature` in the codebase — catches every remaining touch-point that does not carry a TODO marker (the `use` imports, the `group()` and blade ternaries, the `::activate()` calls in tests + migration, and the flag class file itself). Remove the imports, collapse the ternaries to their new-name branch, drop the `::activate()` calls from the test `beforeEach`s, then delete `app/Features/CustomAdvisorRenameFeature.php`.
