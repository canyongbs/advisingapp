---
title: Cleanup Tiptap Content For Case Management And Meeting Center
created: 2026-06-18
---

## Feature Flags

## Temporary Migrations

These are one-time data migrations that convert stored content from the legacy Tiptap block format (`tiptapBlock`) to the RichEditor custom block format (`customBlock`). Once each has run on all tenants, delete the migration together with its test so the two stay in sync.

- Delete `app-modules/case-management/database/migrations/2026_06_18_234750_tmp_migrate_from_content_tiptap_to_richeditor_for_case_management.php`

- Delete `app-modules/meeting-center/database/migrations/2026_06_18_153618_tmp_migrate_from_content_tiptap_to_richeditor_for_meeting_center.php`

- In `tests/TenantMigrationTests.php`: remove the tests for the two migrations above ("2026_06_18_234750 converts ..." and "2026_06_18_153618 converts ...") along with their fixture helpers `oldTiptapCaseFormContent()`, `oldTiptapCaseEmailTemplateBody()`, and `oldTiptapEventRegistrationFormContent()`

## Additional Cleanup

The code below still understands both the new `customBlock` format and the legacy `tiptapBlock` format so that content renders correctly during the rollout window (while some tenants have run the migrations above and others have not). Once every Tiptap-to-RichEditor migration — forms/surveys/applications, case management, and meeting center — has run on all tenants, all stored content is `customBlock` and the legacy handling becomes dead code that can be removed.

- In `app-modules/case-management/src/Notifications/Concerns/HandlesCaseTemplateContent.php`: remove the `'tiptapBlock'` branches from `getBlockId()` and `setBlockConfigUrl()` — only the `'customBlock'` case is needed.

- In `app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php`: remove the `'tiptapBlock'` branch so only `'customBlock'` is handled, and rename the class (it is no longer Tiptap-specific).

- In `app-modules/form/src/Actions/GenerateFormKitSchema.php`: remove the `'tiptapBlock'` arm from the block-type match — only `'customBlock'` is needed.
