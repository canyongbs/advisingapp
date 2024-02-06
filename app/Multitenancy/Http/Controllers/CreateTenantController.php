<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Multitenancy\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Multitenancy\Actions\CreateTenant;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\Http\Requests\CreateTenantRequest;

class CreateTenantController extends Controller
{
    public function __invoke(CreateTenantRequest $request)
    {
        try {
            DB::beginTransaction();

            $tenant = app(CreateTenant::class)(
                $request->name,
                $request->domain,
                TenantConfig::from($request),
            );

            if (is_null($tenant)) {
                DB::rollBack();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tenant failed to create',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return response('Tenant failed to create', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tenant created successfully!',
                ], Response::HTTP_CREATED);
            }

            return response('Tenant created successfully!', Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();

            report($e);

            if ($e instanceof ValidationException) {
                Log::error($e->getMessage());
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Something went wrong. Please try again.',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response('Something went wrong. Please try again.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
