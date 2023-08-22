<?php

namespace Assist\Authorization\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Filament\Pages\Dashboard;
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
        $socialite = $provider
            ->driver()
            ->setConfig($provider->config())
            ->user();

        $user = User::updateOrCreate(
            ['email' => $socialite->getEmail()],
            [
                'name' => $socialite->getName(),
                'password' => bcrypt(Str::random()),
            ]
        );

        Auth::login($user);

        session(['auth_via' => $provider]);

        return redirect()->to(Dashboard::getUrl());
    }
}
