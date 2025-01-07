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

namespace AdvisingApp\StudentDataModel\Policies;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        if ($authenticatable->canAny(['enrollment.view-any', 'product_admin.view-any'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view enrollments.');
    }

    public function view(Authenticatable $authenticatable, Enrollment $enrollment): Response
    {
        if ($authenticatable->canAny(["enrollment.{$enrollment->getKey()}.view", 'product_admin.*.view'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this enrollment.');
    }

    public function create(Authenticatable $authenticatable): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to create enrollment.');
        }

        if ($authenticatable->canAny(['enrollment.create', 'product_admin.create'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to create enrollment.');
    }

    public function update(Authenticatable $authenticatable, Enrollment $enrollment): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to update enrollments.');
        }

        if ($authenticatable->canAny(['enrollment.*.update', 'product_admin.*.update'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to update enrollments.');
    }

    public function delete(Authenticatable $authenticatable, Enrollment $enrollment): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to delete enrollments.');
        }

        if ($authenticatable->canAny(['enrollment.*.delete', 'product_admin.*.delete'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to delete enrollments.');
    }

    public function restore(Authenticatable $authenticatable, Enrollment $enrollment): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to restore enrollments.');
        }

        if ($authenticatable->canAny(['enrollment.*.restore', 'product_admin.*.restore'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to restore enrollments.');
    }

    public function forceDelete(Authenticatable $authenticatable, Enrollment $enrollment): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to force delete enrollments.');
        }

        if ($authenticatable->canAny(['enrollment.*.force-delete', 'product_admin.*.force-delete'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to force delete enrollments.');
    }

    public function import(Authenticatable $authenticatable): Response
    {
        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return Response::deny('You do not have permission to import enrollments.');
        }

        if ($authenticatable->canAny(['enrollment.create', 'product_admin.create'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to import enrollments.');
    }
}
