---
title: SQS Native Overflow Storage
created: 2026-10-06
---

## Feature Flags

## Temporary Migrations

## Additional Cleanup

- Once the SQS queues' 4-day retention period has passed since this deployed everywhere, search for `TODO: Cleanup Task (sqs-native-overflow)` and remove the legacy pointer handling.
- Delete any leftover `sqs-payloads/` objects from the landlord and tenant S3 buckets.
