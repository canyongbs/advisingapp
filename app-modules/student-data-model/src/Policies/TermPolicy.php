<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Policies;

use AdvisingApp\StudentDataModel\Models\Term;
use App\Models\Authenticatable;
use Filament\Support\Authorization\DenyResponse;
use Illuminate\Auth\Access\Response;

/**
 * Terms synced from the SIS are read-only; terms can only be maintained manually until the SIS syncs any.
 */
class TermPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.view-any',
            denyResponse: 'You do not have permission to view terms.'
        );
    }

    public function view(Authenticatable $authenticatable, Term $term): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.view',
            denyResponse: 'You do not have permission to view this term.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (Term::hasSisTerms()) {
            return Response::deny('Terms cannot be added manually while they are synced from the SIS.');
        }

        return $authenticatable->canOrElse(
            abilities: 'settings.create',
            denyResponse: 'You do not have permission to create terms.'
        );
    }

    public function update(Authenticatable $authenticatable, Term $term): Response
    {
        if (filled($term->sis_term_id)) {
            return Response::deny('Terms synced from the SIS cannot be updated.');
        }

        return $authenticatable->canOrElse(
            abilities: 'settings.*.update',
            denyResponse: 'You do not have permission to update this term.'
        );
    }

    public function delete(Authenticatable $authenticatable, Term $term): Response
    {
        if (filled($term->sis_term_id)) {
            return DenyResponse::make('synced_from_sis', message: fn (int $failureCount, int $totalCount): string => $failureCount === $totalCount
                ? 'All selected terms were synced from the SIS, so cannot be deleted.'
                : "{$failureCount} of the selected terms were synced from the SIS, so cannot be deleted.");
        }

        return $authenticatable->canOrElse(
            abilities: 'settings.*.delete',
            denyResponse: 'You do not have permission to delete this term.'
        );
    }

    public function deleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.delete',
            denyResponse: 'You do not have permission to delete terms.'
        );
    }
}
