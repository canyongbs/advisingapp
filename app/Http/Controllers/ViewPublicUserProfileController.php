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

        $office_hours = collect($user->office_hours)
            ->filter(fn ($data, $day) => data_get($data, 'enabled'))
            ->mapWithKeys(fn ($data, $day) => [
                $day => [
                    'starts_at' => data_get($data, 'starts_at'),
                    'ends_at' => data_get($data, 'ends_at'),
                ],
            ]);

        return [
            'avatar_url' => $user->getFilamentAvatarUrl(),
            'name' => $user->name,
            'email' => $user->email,
            'out_of_office' => $user->out_of_office_is_enabled ? [
                'starts_at' => $user->out_of_office_starts_at,
                'ends_at' => $user->out_of_office_ends_at,
            ] : false,
            'bio' => $user->is_bio_visible_on_profile
                ? $user->bio
                : null,
            'pronouns' => $user->are_pronouns_visible_on_profile
                ? $user->pronouns->label
                : null,
            'timezone' => $user->timezone,
            'office_hours' => $user->office_hours_are_enabled && $office_hours->keys()->count()
                ? $office_hours
                : false,
            'appointments_are_restricted_to_existing_students' => $user->appointments_are_restricted_to_existing_students,
        ];
    }
}
