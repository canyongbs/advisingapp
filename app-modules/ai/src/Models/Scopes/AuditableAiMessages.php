<?php

namespace AdvisingApp\Ai\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Audit\Settings\AuditSettings;
use Illuminate\Database\Eloquent\Relations\Relation;

class AuditableAiMessages
{
    public function __construct(
        protected AuditSettings $settings,
    ) {}

    public function __invoke(Builder | Relation $query): void
    {
        $query->whereDate('created_at', '>=', now()->subDays($this->settings->assistant_chat_message_logs_retention_duration_in_days));
    }
}
