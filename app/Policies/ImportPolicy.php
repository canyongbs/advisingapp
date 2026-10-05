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

namespace App\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Models\Import;
use App\Support\FeatureAccessResponse;
use Illuminate\Auth\Access\Response;

class ImportPolicy
{
    /**
     * Used by Filament to authorize downloading an import's failed rows. Only the user who ran the import may do so,
     * matching Filament's behaviour without a policy, and not while the importer's feature is disabled.
     */
    public function view(Authenticatable $authenticatable, Import $import): Response
    {
        if (in_array($import->importer, Feature::getDisabledImporterAndExporterClasses(), true)) {
            return FeatureAccessResponse::deny();
        }

        return $import->user()->is($authenticatable)
            ? Response::allow()
            : Response::deny('You do not have permission to view this import.');
    }
}
