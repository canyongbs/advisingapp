<?php

namespace Assist\Engagement\DataTransferObjects;

use App\Models\User;
use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class EngagementBatchCreationData extends Data
{
    public function __construct(
        public User $user,
        public Collection $records,
        public string $body,
        public array $deliveryMethods,
        public ?string $subject = null,
    ) {}
}
