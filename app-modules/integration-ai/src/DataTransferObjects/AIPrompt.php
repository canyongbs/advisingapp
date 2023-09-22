<?php

namespace Assist\IntegrationAI\DataTransferObjects;

use Carbon\Carbon;
use App\Models\User;
use Spatie\LaravelData\Data;

class AIPrompt extends Data
{
    public User $user;

    public array $request;

    public Carbon $timestamp;

    public string $message;

    public array $metadata;
}
