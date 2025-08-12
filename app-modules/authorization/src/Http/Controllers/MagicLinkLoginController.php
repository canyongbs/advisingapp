<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class MagicLinkLoginController
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, LoginMagicLink $magicLink): RedirectResponse
    {
        abort_if(
            boolean: now()->greaterThanOrEqualTo($magicLink->created_at->addMinutes(15))
                || $magicLink->used_at !== null,
            code: 403,
            message: 'Invalid link. Please request a new one.'
        );

        $payload = Crypt::decrypt(urldecode($request->get('code')));

        $payloadUserId = $payload['user_id'];

        abort_if(
            boolean: $payloadUserId !== $magicLink->user_id,
            code: 403,
            message: 'Invalid link. Please request a new one.'
        );

        $user = User::findOrFail($payloadUserId);

        $panel = Filament::getPanel('admin');

        $magicLink->used_at = now();

        $magicLink->saveOrFail();

        Auth::guard($panel->getAuthGuard())->login($user);

        return redirect()
            ->to($panel->getHomeUrl());
    }
}
