<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ViewPublicUserProfileController extends Controller
{
    public function __invoke(Request $request, User $user): View
    {
        abort_unless($user->has_enabled_public_profile, Response::HTTP_NOT_FOUND);

        $officeHours = $this->formatHours($user->office_hours);

        $workingHours = $this->formatHours($user->working_hours);

        return view('user-profile-public', [
            'data' => [
                'avatar_url' => $user->getFilamentAvatarUrl(),
                'name' => $user->name,
                'email' => $user->is_email_visible_on_profile ? $user->email : null,
                'phone_number' => $user->is_phone_number_visible_on_profile ? $user->phone_number : null,
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
                'office_hours' => $user->office_hours_are_enabled && $officeHours->keys()->count()
                    ? $officeHours
                    : false,
                'appointments_are_restricted_to_existing_students' => $user->appointments_are_restricted_to_existing_students,
                'working_hours' => $user->working_hours_are_enabled && $user->are_working_hours_visible_on_profile && $workingHours->keys()->count()
                    ? $workingHours
                    : false,
            ],
        ]);
    }

    /**
     * @param array<mixed>|null $hours
     *
     * @return Collection<string, mixed>
     */
    private function formatHours(?array $hours): Collection
    {
        return collect($hours)
            ->filter(fn ($data, $day) => data_get($data, 'enabled'))
            ->mapWithKeys(fn ($data, $day) => [
                $day => [
                    'starts_at' => data_get($data, 'starts_at'),
                    'ends_at' => data_get($data, 'ends_at'),
                ],
            ]);
    }
}
