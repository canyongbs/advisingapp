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
