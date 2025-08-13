<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Http\Requests\GenerateLoginMagicLinkRequest;
use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            ]);

            $user->deleted_at = null;

            if ($user->isDirty()) {
                $user->saveOrFail();
            }

            foreach (LicenseType::cases() as $license) {
                $user->grantLicense($license);
            }

            $user->syncRoles($data['type']);

            // Remove any existing magic links for this user
            LoginMagicLink::query()
                ->where('user_id', $user->getKey())
                ->delete();

            $code = Str::random();

            $magicLink = new LoginMagicLink();
            $magicLink->user()->associate($user);
            $magicLink->code = Hash::make($code);
            $magicLink->saveOrFail();

            DB::commit();

            return response()->json([
                'link' => URL::temporarySignedRoute(
                    name: 'magic-link.login',
                    expiration: now()->addMinutes(10)->toImmutable(),
                    parameters: [
                        'magicLink' => $magicLink->getKey(),
                        'payload' => urlencode(
                            Crypt::encrypt(
                                [
                                    'code' => $code,
                                    'user_id' => $user->getKey(),
                                ]
                            )
                        ),
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
