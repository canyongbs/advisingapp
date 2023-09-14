<?php

namespace Assist\IntegrationAI\DataTransferObjects;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class AIPrompt extends Data
{
    public User $user;

    public Request $request;

    public Carbon $timestamp;

    public string $message;

    public array $metadata;
}
