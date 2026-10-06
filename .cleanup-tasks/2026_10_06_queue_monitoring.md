---
title: Queue Monitoring
created: 2026-10-06
---

## Feature Flags

- App\Features\QueueMonitoringFeature

## Temporary Migrations

## Additional Cleanup

- Search for `TODO: Cleanup Task (queue-monitoring)` and follow the instructions at each site.
- Delete the queue autoscaling deployment runbook from the devops repository (`ecs/advisingapp/deploy-queue-autoscaling.md`, the `docker/devops` submodule) once the release is out in every environment.
