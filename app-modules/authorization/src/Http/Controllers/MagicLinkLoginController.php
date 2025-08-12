<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Models\LoginMagicLink;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLinkLoginController
{
    public function __invoke(Request $request, LoginMagicLink $magicLink): RedirectResponse
    {
        abort_if(
            boolean: now()->greaterThanOrEqualTo($magicLink->created_at->addMinutes(15))
                || $magicLink->used_at !== null,
            code: 403,
            message: 'Invalid link. Please request a new one.'
        );

        $user = $magicLink->user;

        abort_if(
            boolean: $user === null,
            code: 404,
            message: 'User not found.'
        );

        $panel = Filament::getPanel('admin');

        Auth::guard($panel->getAuthGuard())
            ->login($user);

        return redirect()
            ->intended($panel->getHomeUrl());
    }
}
