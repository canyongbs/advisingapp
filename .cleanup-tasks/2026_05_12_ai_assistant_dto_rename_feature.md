---
title: AI Assistant DTO Rename Feature
created: 2026-05-12
---

## Feature Flags

- App\Features\AiAssistantDtoRenameFeature

## Temporary Migrations

- database/migrations/2026_05_12_151030_tmp_data_rename_license_settings_subscription_keys.php

## Additional Cleanup

- Search for `TODO: AiAssistantDtoRenameFeature cleanup` in the codebase — every gated branch has an inline note explaining what to keep and what to delete.
- In [app/DataTransferObjects/LicenseManagement/LicenseAddonsData.php](../app/DataTransferObjects/LicenseManagement/LicenseAddonsData.php): remove the old `$customAiAssistants` and `$qnaAdvisor` properties (keep `$employeeAdvisors` and `$customerAdvisors`).
- In [app/DataTransferObjects/LicenseManagement/LicenseLimitsData.php](../app/DataTransferObjects/LicenseManagement/LicenseLimitsData.php): remove the old `$conversationalAiAssistants` and `$qnaAdvisorsCount` properties (keep `$employeeAdvisors` and `$customerAdvisorsCount`).
- In [app/Enums/Feature.php](../app/Enums/Feature.php): remove the old `CustomAiAssistants` and `QnAAdvisor` enum cases (keep `EmployeeAdvisors` and `CustomerAdvisors`).
- In [app/Filament/Pages/ManageLicenseSettings.php](../app/Filament/Pages/ManageLicenseSettings.php): remove the `AiAssistantDtoRenameFeature::active()` visibility toggles on the old and new form fields — keep only the new `employeeAdvisors` and `customerAdvisors` toggles and limit inputs, and delete the old `customAiAssistants`, `qnaAdvisor`, `conversationalAiAssistants`, and `qnaAdvisorsCount` fields.
- In [app/Http/Requests/Tenants/CreateTenantRequest.php](../app/Http/Requests/Tenants/CreateTenantRequest.php): uncomment the four commented-out `required` rules (`limits.employeeAdvisors`, `limits.customerAdvisorsCount`, `addons.employeeAdvisors`, `addons.customerAdvisors`); remove the entire `sometimes` block beneath (all eight `$rules[...]` assignments for both old and new names, plus the stray `// }` comment); and collapse back to a single returned array literal.
- In [app/Http/Requests/Tenants/SyncTenantRequest.php](../app/Http/Requests/Tenants/SyncTenantRequest.php): uncomment the four commented-out `required` rules (`limits.employeeAdvisors`, `limits.customerAdvisorsCount`, `addons.employeeAdvisors`, `addons.customerAdvisors`); remove the entire `sometimes` block beneath (all eight `$rules[...]` assignments for both old and new names); and collapse back to a single returned array literal.
- In [app-modules/ai/src/Filament/Resources/AiAssistants/Pages/EditAiAssistant.php](../app-modules/ai/src/Filament/Resources/AiAssistants/Pages/EditAiAssistant.php): remove the `AiAssistantDtoRenameFeature::active()` ternary in the assistants limit check — keep only `app(LicenseSettings::class)->data->limits->employeeAdvisors`.
- In [app-modules/ai/src/Policies/AiAssistantPolicy.php](../app-modules/ai/src/Policies/AiAssistantPolicy.php): remove the `AiAssistantDtoRenameFeature::active()` ternaries in `viewAny()` and `create()` — keep only `Feature::EmployeeAdvisors->getGateName()` and `app(LicenseSettings::class)->data->limits->employeeAdvisors`.
- In [app-modules/ai/src/Policies/QnaAdvisorPolicy.php](../app-modules/ai/src/Policies/QnaAdvisorPolicy.php): remove the `AiAssistantDtoRenameFeature::active()` ternary — keep only `Feature::CustomerAdvisors->getGateName()`.
- In [app-modules/report/src/Filament/Pages/CustomAdvisorReport.php](../app-modules/report/src/Filament/Pages/CustomAdvisorReport.php): remove the `AiAssistantDtoRenameFeature::active()` ternary in `canAccess()` — keep only `Feature::EmployeeAdvisors->getGateName()`.
- In [app-modules/report/src/Filament/Pages/QnaAdvisorReport.php](../app-modules/report/src/Filament/Pages/QnaAdvisorReport.php): remove the `AiAssistantDtoRenameFeature::active()` ternary in `canAccess()` — keep only `Feature::CustomerAdvisors->getGateName()`.
- In [app-modules/ai/tests/Tenant/Filament/Resources/AiAssistants/Pages/ManageEmployeeAdvisorCategoriesTest.php](../app-modules/ai/tests/Tenant/Filament/Resources/AiAssistants/Pages/ManageEmployeeAdvisorCategoriesTest.php): remove the `AiAssistantDtoRenameFeature::activate();` call from `beforeEach` and remove the corresponding `use App\Features\AiAssistantDtoRenameFeature;` import.
- In [app-modules/ai/tests/Tenant/Filament/Resources/AiAssistants/Pages/ManageEmployeeAdvisorQuestionsTest.php](../app-modules/ai/tests/Tenant/Filament/Resources/AiAssistants/Pages/ManageEmployeeAdvisorQuestionsTest.php): remove the `AiAssistantDtoRenameFeature::activate();` call from `beforeEach` and remove the corresponding `use App\Features\AiAssistantDtoRenameFeature;` import.
- Remove every remaining `use App\Features\AiAssistantDtoRenameFeature;` import that becomes unused after the above edits.
- Delete the feature flag class itself: `app/Features/AiAssistantDtoRenameFeature.php`.
