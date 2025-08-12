<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Http\Requests\GenerateLoginMagicLinkRequest;
use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class GenerateLoginMagicLinkController
{
    /**
     * @throws Throwable
     */
    public function __invoke(GenerateLoginMagicLinkRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $user = User::query()
                ->withTrashed()
                // Or Upsert? Should we even be getting the User yet?
                ->upsertReturning(
                    values: [
                        'email' => $data['email'],
                    ],
                    uniqueBy: 'email',
                    update: [
                        'name' => $data['name'],
                        'email_verified_at' => now(),
                        'is_external' => true,
                    ]
                )
                ->first();

            $magicLink = new LoginMagicLink();

            $magicLink->user()->associate($user);
            $magicLink->code = Str::uuid7();

            $magicLink->saveOrFail();

            DB::commit();

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
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return response()->json([
                'error' => 'Failed to generate magic link.',
            ], 500);
        }
    }
}
