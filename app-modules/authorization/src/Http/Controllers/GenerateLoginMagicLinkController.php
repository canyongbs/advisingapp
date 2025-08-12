<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Http\Requests\GenerateLoginMagicLinkRequest;
use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
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
                ->firstOrCreate(
                    attributes: [
                        'email' => $data['email'],
                    ],
                    values: [
                        'name' => $data['name'],
                        'email_verified_at' => now(),
                        'is_external' => true,
                    ]
                );

            assert($user instanceof User); // @phpstan-ignore-line

            $user->fill([
                'name' => $data['name'],
                'email_verified_at' => now(),
                'is_external' => true,
                'deleted_at' => null,
            ]);

            if ($user->isDirty()) {
                $user->saveOrFail();
            }

            foreach (LicenseType::cases() as $license) {
                $user->grantLicense($license);
            }

            $user->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

            // Remove any existing magic links for this user
            LoginMagicLink::query()
                ->where('user_id', $user->getKey())
                ->delete();

            $magicLink = new LoginMagicLink();
            $magicLink->user()->associate($user);
            $magicLink->code = urlencode(
                Crypt::encrypt(
                    [
                        'key' => Str::random(),
                        'user_id' => $user->getKey(),
                    ]
                )
            );
            $magicLink->saveOrFail();

            DB::commit();

            return response()->json([
                'link' => URL::temporarySignedRoute(
                    name: 'magic-link.login',
                    expiration: now()->addMinutes(10)->toImmutable(),
                    parameters: [
                        'magicLink' => $magicLink->getKey(),
                        'code' => $magicLink->code,
                    ],
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
