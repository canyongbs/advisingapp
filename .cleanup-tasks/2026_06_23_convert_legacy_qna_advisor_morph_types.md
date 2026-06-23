---
title: Convert Legacy QNA Advisor Morph Types
created: 2026-06-23
---

## Feature Flags

## Temporary Migrations

- app-modules/ai/database/migrations/2026_06_23_143052_tmp_data_convert_legacy_qna_advisor_morph_types_to_customer_advisor.php

## Additional Cleanup

- Remove the `CustomerAdvisorFileFactory` if it was only created to support the migration test
