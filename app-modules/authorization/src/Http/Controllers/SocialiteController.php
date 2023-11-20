<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Authorization\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Assist\Authorization\Enums\SocialiteProvider;

class SocialiteController extends Controller
{
    public function redirect(SocialiteProvider $provider, Request $request)
    {
        // Regenerate session and logout user to try to fix InvalidStateException
        if ($request->hasSession()) {
            $request->session()->regenerate(true);
        }

        auth()->guard('web')->logout();

        return $provider->driver()
            ->setConfig($provider->config())
            ->redirect();
    }

    public function callback(SocialiteProvider $provider)
    {
        $socialiteUser = $provider
            ->driver()
            ->setConfig($provider->config())
            ->user();

        /** @var User $user */
        $user = User::query()
            ->where('email', $socialiteUser->getEmail())
            ->first();

        if (! $user?->is_external) {
            Notification::make()
                ->title('A user with that email address not found. Please contact your administrator.')
                ->danger()
                ->send();

            return redirect()->to(Filament::getLoginUrl());
        }

        if ($provider === SocialiteProvider::Azure) {
            $request = Http::withToken($socialiteUser->token)
                ->contentType('image/jpeg')
                ->get('https://graph.microsoft.com/v1.0/me/photo/$value');

            $user->addMediaFromString($request->body())->usingFileName(Str::uuid() . '.jpg')->toMediaCollection('avatar');
        }

        $user->update([
            'name' => $socialiteUser->getName(),
            'avatar_url' => $socialiteUser->getAvatar(),
        ]);

        Auth::login($user);

        session(['auth_via' => $provider]);

        return redirect()->to(Filament::getUrl());
    }
}
