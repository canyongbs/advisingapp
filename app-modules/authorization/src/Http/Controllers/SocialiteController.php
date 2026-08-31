<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Enums\SocialiteProvider;
use App\Exceptions\InvalidUserAvatarMimeType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class SocialiteController extends Controller
{
    public function redirect(SocialiteProvider $provider, Request $request)
    {
        $intendedUrl = $request->session()->pull('url.intended');

        // Regenerate session and logout user to try to fix InvalidStateException
        if ($request->hasSession()) {
            $request->session()->regenerate(true);
        }

        auth()->guard('web')->logout();

        if ($intendedUrl) {
            $request->session()->put('url.intended', $intendedUrl);
        }

        $driver = $provider->driver()
            ->setConfig($provider->config());

        if (in_array($provider, [SocialiteProvider::Azure, SocialiteProvider::Google])) {
            $driver->with(['prompt' => 'select_account']);
        }

        return $driver->redirect();
    }

    public function callback(SocialiteProvider $provider)
    {
        $socialiteUser = $provider
            ->driver()
            ->setConfig($provider->config())
            ->user();

        /** @var User $user */
        $user = User::query()
            ->where(new Expression('lower(email)'), strtolower($provider->getEmailFromUser($socialiteUser)))
            ->first();

        if (! $user?->is_external) {
            Notification::make()
                ->title('A user with that email address not found. Please contact your administrator.')
                ->danger()
                ->send();

            return redirect()->to(Filament::getLoginUrl());
        }

        if ($provider === SocialiteProvider::Azure) {
            try {
                // Retry transient Azure failures (connection errors, rate limits, server errors) but not client errors.
                $response = Http::withToken($socialiteUser->token)
                    ->dontTruncateExceptions()
                    ->retry(3, 500, when: function (?Throwable $exception): bool {
                        if ($exception instanceof ConnectionException) {
                            return true;
                        }

                        return $exception instanceof RequestException
                            && ($exception->response->serverError() || $exception->response->status() === 429);
                    }, throw: false)
                    ->get('https://graph.microsoft.com/v1.0/me/photo/$value');

                if ($response->successful()) {
                    $mimeType = $response->header('Content-Type');

                    if (in_array($mimeType, ['image/png', 'image/jpeg', 'image/webp', 'image/jpg', 'image/svg+xml'])) {
                        $extension = (new MimeTypes())->getExtensions($mimeType)[0] ?? null;

                        $body = $response->body();

                        if ($extension && $body) {
                            $media = $user->addMediaFromString($body)->usingFileName(Str::uuid() . '.' . $extension)->toMediaCollection('avatar');

                            if (is_null($media->created_by_id)) {
                                $media->createdBy()->associate($user);
                                $media->saveQuietly();
                            }
                        }
                    } else {
                        throw new InvalidUserAvatarMimeType($mimeType, $user);
                    }
                } elseif ($response->failed() && $response->status() !== 404) {
                    // A 404 means the user has no Azure profile photo, which is expected and safe to ignore.
                    report($response->toException());
                }
            } catch (Throwable $exception) {
                // Fetching the avatar is best-effort; a failure (including an exhausted-retry ConnectionException) must not block login.
                report($exception);
            }
        }

        $user->update([
            'name' => $socialiteUser->getName(),
            'avatar_url' => $socialiteUser->getAvatar(),
        ]);

        Auth::login($user);

        session(['auth_via' => $provider]);

        return redirect()->intended(Filament::getUrl());
    }
}
