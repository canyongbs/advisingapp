<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Multitenancy\Actions\CreateTenant;
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

                return response()->json([
                    'message' => 'Tenant failed to create',
                ], 500);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tenant created successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
