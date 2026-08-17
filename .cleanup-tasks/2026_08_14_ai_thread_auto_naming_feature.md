---
title: AI Thread Auto Naming Feature
created: 2026-08-14
---

## Feature Flags

- App\Features\AiThreadAutoNamingFeature

## Temporary Migrations

## Additional Cleanup

- In `BaseOpenAiService::sendNewMessage()`, delete the entire inner try/catch block inside the outer `finally` that names the thread when `AiThreadAutoNamingFeature` is inactive.
- In `TestAiService::sendMessage()`, delete the `if` block that fakes a thread name when `AiThreadAutoNamingFeature` is inactive, for the same reason.
