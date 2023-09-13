<?php

namespace Assist\Authorization\Http\Controllers\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class OneTimeLoginController extends Controller
{
    public function __invoke(User $user): RedirectResponse
    {
        if (filled($user->password) || $user->is_external) {
            abort(403);
        }

        auth()->login($user);

        return redirect(Filament::getUrl());
    }
}
