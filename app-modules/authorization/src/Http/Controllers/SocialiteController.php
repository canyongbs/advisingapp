<?php

namespace Assist\Authorization\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        $user = User::query()
            ->where('email', $socialiteUser->getEmail())
            ->first();

        if (! $user?->is_external) {
            abort(403);
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
