<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Policies;

use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\SettingsPermissions;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class InteractionRelationPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return Response::deny('You are not licensed for the Retention or Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.view-any',
                denyResponse: 'You do not have permission to view interaction relations.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: 'product_admin.view-any',
            denyResponse: 'You do not have permission to view interaction relations.'
        );
    }

    public function view(Authenticatable $authenticatable, InteractionRelation $relation): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.view',
                denyResponse: 'You do not have permission to view this interaction relation.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$relation->getKey()}.view"],
            denyResponse: 'You do not have permission to view this interaction relation.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.create',
                denyResponse: 'You do not have permission to create interaction relations.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create interaction relations.'
        );
    }

    public function update(Authenticatable $authenticatable, InteractionRelation $relation): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.update',
                denyResponse: 'You do not have permission to update this interaction relation.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$relation->getKey()}.update"],
            denyResponse: 'You do not have permission to update this interaction relation.'
        );
    }

    public function delete(Authenticatable $authenticatable, InteractionRelation $relation): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.delete',
                denyResponse: 'You do not have permission to delete this interaction relation.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$relation->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this interaction relation.'
        );
    }

    public function restore(Authenticatable $authenticatable, InteractionRelation $relation): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.restore',
                denyResponse: 'You do not have permission to restore this interaction relation.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$relation->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this interaction relation.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, InteractionRelation $relation): Response
    {
        if (SettingsPermissions::active()) {
            return $authenticatable->canOrElse(
                abilities: 'settings.*.force-delete',
                denyResponse: 'You do not have permission to permanently delete this interaction relation.'
            );
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$relation->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction relation.'
        );
    }
}
