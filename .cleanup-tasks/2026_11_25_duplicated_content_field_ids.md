---
title: Duplicated Content Field IDs
created: 2026-11-25
ready: true
---

## Feature Flags

## Temporary Migrations

- app-modules/form/database/migrations/2026_11_25_135940_tmp_repair_duplicated_form_content_field_ids.php
- app-modules/application/database/migrations/2026_11_25_135942_tmp_repair_duplicated_application_content_field_ids.php
- app-modules/meeting-center/database/migrations/2026_11_25_135944_tmp_repair_duplicated_event_registration_form_content_field_ids.php
- app-modules/survey/database/migrations/2026_11_25_143404_tmp_repair_duplicated_survey_content_field_ids.php

## Additional Cleanup

- Search for `TODO: Cleanup Task (duplicated-content-field-ids)`.

<!--
Only list cleanup that has no home in code. Do NOT restate obvious feature-flag
removals (keep the active path, drop the inactive path), and do NOT include file
paths or line numbers. For non-obvious changes, put a `TODO: Cleanup Task (<tag>)`
comment at the change site and reference the tag here — e.g.
"Search for `TODO: Cleanup Task (some-feature)`".
-->
