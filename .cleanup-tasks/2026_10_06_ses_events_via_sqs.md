---
title: SES Events via SQS
created: 2026-10-06
---

## Feature Flags

- App\Features\SesEventDeduplicationFeature

## Temporary Migrations

- database/migrations/2026_10_06_234840_tmp_backfill_ses_event_occurred_at_from_payload.php

## Additional Cleanup

- Search for `TODO: Cleanup Task (ses-sqs)` and follow the instructions at each site.
- Once the HTTP SNS subscription to the SES webhook has been removed in every environment, also remove the `VerifyAwsSnsRequest` and `HandleAwsSnsRequest` middleware, which only that route uses.
