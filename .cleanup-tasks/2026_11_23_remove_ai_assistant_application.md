---
title: Remove Ai Assistant Application
created: 2026-11-23
---

## Feature Flags

- App\Features\RemoveAiAssistantApplicationFeature

## Temporary Migrations

## Additional Cleanup

- Add a permanent migration that drops `application` from `ai_assistants` and `ai_thread_folders`.
- Delete `AdvisingApp\Ai\Enums\AiAssistantApplication`.
- Remove `application` from `AdvisingApp\Ai\Models\AiAssistant` and `AdvisingApp\Ai\Models\AiThreadFolder` `$fillable` and `$casts`.
- Search for `TODO: Cleanup Task (remove-ai-assistant-application)` and complete each site.
- Delete `App\Features\RemoveAiAssistantApplicationFeature` and `2026_11_26_113141_make_ai_assistant_application_columns_nullable_and_activate_flag.php`, keeping only active code branches.
