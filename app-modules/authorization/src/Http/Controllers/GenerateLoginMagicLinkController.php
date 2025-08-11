<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Http\Requests\GenerateLoginMagicLinkRequest;
use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class GenerateLoginMagicLinkController
{
    public function __invoke(GenerateLoginMagicLinkRequest $request): JsonResponse
    {
        $user = User::query()
            ->withTrashed()
            // Or Upsert? Should we even be getting the User yet?
            ->firstOrCreate(
                [
                    'name' => 'Global Admin',
                    'email' => 'globaladmin@canyongbs.com',
                    'is_external' => true,
                ],
                [
                    'email_verified_at' => now(),
                ]
            );

        $magicLink = new LoginMagicLink();

        $magicLink->user()->associate($user);
        $magicLink->code = Str::uuid7();

        $magicLink->saveOrFail();

        return response()->json([
            'link' => URL::temporarySignedRoute(
                name: 'magic-link.login',
                expiration: now()->addMinutes(10)->toImmutable(),
                parameters: [
                    'code' => $magicLink->code,
                ],
                absolute: false,
            ),
        ]);
    }
}
