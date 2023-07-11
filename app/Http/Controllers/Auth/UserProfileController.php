<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        $this->authorize('auth_profile_edit');

        return view('profile.show');
    }
}
