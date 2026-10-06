---
paths:
    - '{app,app-modules,config,routes,docker}/**'
---

# Redis

## Never flush a Redis cache store

All Redis connections share one ElastiCache Serverless cluster (local: a 3-node cluster). There FLUSHDB/FLUSHALL wipe every key, including sessions, cache for all tenants, and queue autoscaling state. Never call `Cache::flush()`/`Cache::store(...)->flush()` or run `cache:clear` in deployed environments, and keep the SQS `overflow.flush_on_clear` option false (with no overflow store set, it flushes the default cache). Invalidate with `Cache::forget()` or `Cache::tags([...])->flush()` instead.
