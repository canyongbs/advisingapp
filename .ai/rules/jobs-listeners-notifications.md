---
paths:
    - '**/{Jobs,Listeners,Notifications}/**/*.php'
---

# Jobs Listeners Notifications

## Queued job timeouts must stay under the 1200s SQS visibility timeout

Every SQS queue uses a 20-minute (1200s) visibility timeout, and SQS ignores retry_after. A queued job, listener, or notification with a `$timeout` (or #[Timeout]) of 1200s or more is redelivered while still running, so with low `$tries` it is marked failed mid-run. Keep timeouts at or below 1140s (leave a margin), and split longer work into smaller jobs instead of raising the timeout.
