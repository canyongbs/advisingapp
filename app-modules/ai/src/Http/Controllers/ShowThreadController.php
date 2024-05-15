<?php

namespace AdvisingApp\Ai\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;

class ShowThreadController
{
    public function __invoke(AiThread $thread): JsonResponse
    {
        if (! $thread->user()->is(auth()->user())) {
            abort(404);
        }

        return response()->json([
            'messages' => $thread->messages()
                ->oldest()
                ->get()
                ->toBase()
                ->map(fn (AiMessage $message): array => $message->attributesToArray())
                ->all(),
            'users' => $thread->users()
                ->distinct()
                ->get()
                ->toBase()
                ->mapWithKeys(fn (User $user): array => [$user->id => [
                    'name' => $user->name,
                    'avatar_url' => $user->getFilamentAvatarUrl(),
                ]])
                ->all(),
        ]);
    }
}
