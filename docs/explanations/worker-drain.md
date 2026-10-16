# Worker Drain

Worker tasks hold ECS scale-in protection while they run jobs. Without a drain, a busy task would keep renewing that protection and keep taking work, so a deployment or scale-in could wait on it indefinitely. The drain stops a task taking new jobs once ECS wants it gone, lets its current jobs finish, then releases the protection.

## When a task drains

Each worker task runs a drain watcher (`php artisan ecs:worker-drain-watch`, its own s6 service). About every 10 seconds (randomised between 8 and 12, so tasks started together do not call ECS at the same instant) it checks whether ECS wants the task gone:

- **Deployment:** the task was started by a deployment that is no longer the service's primary one.
- **Scale-in:** the service has a single deployment, has had more tasks ECS intends to keep running than its desired count for at least 30 seconds, and this task is one of the least busy (earliest protection expiry).
    - Excess that ECS deals with itself does not count: an unprotected task (ECS stops it) or an unhealthy one (ECS runs an extra task while replacing it).
    - Tasks claim drain slots in the landlord cache, so no more tasks drain than the scale-in needs.

`php artisan ecs:worker-drain` drains a task by hand, and `--cancel` resumes it.

## What a draining task does

1. Writes the drain file (see `App\Support\Ecs\WorkerDrainState`), which keeps the healthcheck passing while the autoscaler is stopped, for up to 30 minutes from the start of the drain.
2. Stops the `queue-autoscale` s6 service. The manager sends SIGTERM to its workers, which finish their current job and take no more. s6 does not restart it.
3. Once the manager has exited and no `queue:work` process is left, releases the task's scale-in protection, so ECS stops the task straight away.

A scale-in drain that is no longer needed (ECS stopped another task, or the desired count went back up) is cancelled: the watcher starts `queue-autoscale` again and the task takes jobs. A deployment drain is never cancelled.

Any failed check (AWS, task metadata, cache) changes nothing, so the task stays protected and never cuts off a job. A check that keeps failing the same way is logged once, then `Worker drain checks are working again` is logged when it recovers.

## Timings

- A healthy drain is over within about 21 minutes: the manager waits at most 1200 seconds for its workers, then the release and stop take about a minute.
- If the drain file is still there after 30 minutes, something is stuck (for example the watcher keeps crashing). The healthcheck then fails and ECS replaces the task, and the watcher reports `WorkerDrainOverdueException` once.
- This includes a manual drain left in place: about 30 minutes after `ecs:worker-drain`, the task is replaced unless the drain was cancelled first.

## ECS API usage

ECS API calls are free but rate-limited per account and Region, and the limit is shared with the console, deployments and Application Auto Scaling. Most checks make a single `DescribeServices` call. The task list, task health and protection (`ListTasks`, `DescribeTasks`, `GetTaskProtection`) are only fetched when the service has more tasks running than desired.

## Related protection behaviour

- Jobs with no `$timeout` of their own are protected for the worker timeout of their queue, so nearly every job protects its task.
- Concurrent protection requests on one task are serialised with a file lock, so a shorter request cannot replace a longer protection window.
