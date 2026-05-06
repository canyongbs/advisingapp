---
title: Admissions Stage Workflow Triggers Feature
created: 2026-05-05
---

## Feature Flags

- App\Features\AdmissionsStageWorkflowTriggersFeature

## Temporary Migrations

- database/migrations/2026_05_04_172646_tmp_data_backfill_application_workflow_triggers_with_default_stage.php

## Additional Cleanup

- Search for `TODO: Cleanup Task` in the codebase — every gated branch has an inline note explaining what to keep and what to delete.
- Delete the legacy listener: [app-modules/application/src/Listeners/TriggerApplicationSubmissionWorkflows.php](../app-modules/application/src/Listeners/TriggerApplicationSubmissionWorkflows.php).
- In [app-modules/application/src/Providers/ApplicationServiceProvider.php](../app-modules/application/src/Providers/ApplicationServiceProvider.php) `registerEvents()`: remove the `Event::listen(ApplicationSubmissionCreated::class, TriggerApplicationSubmissionWorkflows::class)` call and the matching `use` import.
- In [app-modules/application/src/Listeners/TriggerApplicationSubmissionStageWorkflows.php](../app-modules/application/src/Listeners/TriggerApplicationSubmissionStageWorkflows.php): remove the `if (! AdmissionsStageWorkflowTriggersFeature::active()) return;` guard at the top of `handleEntered()` and `handleExited()`, plus the `AdmissionsStageWorkflowTriggersFeature` import.
- In [app-modules/application/src/Filament/Forms/ApplicationWorkflowForm.php](../app-modules/application/src/Filament/Forms/ApplicationWorkflowForm.php) `configureForm()`: drop the `AdmissionsStageWorkflowTriggersFeature::active()` check from both `visible()` callbacks (the registry already gates these fields to application workflows; once the FF is gone, they should always render). Also remove the `AdmissionsStageWorkflowTriggersFeature` import.
- In [app-modules/application/src/Filament/Resources/Applications/Pages/ManageApplicationWorkflows.php](../app-modules/application/src/Filament/Resources/Applications/Pages/ManageApplicationWorkflows.php):
    - `getDefaultActiveTab()` and `getTabs()`: delete the `if (! AdmissionsStageWorkflowTriggersFeature::active())` early-returns.
    - `table()`: drop the `->visible(fn (): bool => AdmissionsStageWorkflowTriggersFeature::active())` calls on the "Stage" and "Trigger" columns.
    - `getHeaderActions()`: delete the `if (AdmissionsStageWorkflowTriggersFeature::active()) { ... }` wrapper around the slide-over schema (keep the inner builder calls — they become the only path), and drop the ternaries inside the action callback so it always passes `$data['sub_related_id']` and `$data['event']` straight through (the `sub_related_type` value stays hard-coded to `(new ApplicationSubmissionState)->getMorphClass()`).
- Remove every remaining `use App\Features\AdmissionsStageWorkflowTriggersFeature;` import that becomes unused after the above edits.
- Delete the feature flag class itself: `app/Features/AdmissionsStageWorkflowTriggersFeature.php`.
