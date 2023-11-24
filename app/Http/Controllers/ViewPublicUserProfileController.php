<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewPublicUserProfileController extends Controller
{
    public function __invoke(Request $request, User $user): array
    {
        abort_unless($user->has_enabled_public_profile, Response::HTTP_NOT_FOUND);

        return [
            'avatar_url' => $user->getFilamentAvatarUrl(),
            'name' => $user->name,
            'email' => $user->email,
            'out_of_office' => false, //TODO: needs other pr merged
            'bio' => $user->is_bio_visible_on_profile ? $user->bio : null,
            'pronouns' => $user->are_pronouns_visible_on_profile ? $user->pronouns->label : null,
            'timezone' => $user->timezone,
            'office_hours' => false, //TODO: needs other pr merged
        ];
    }
}
