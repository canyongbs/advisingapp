<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SetAzureSsoSettingRequest;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;

class SetAzureSsoSettingController extends Controller
{
    public function __invoke(SetAzureSsoSettingRequest $request)
    {
        $azureSsoSettings = app(AzureSsoSettings::class);

        $azureSsoSettings->is_enabled = $request->input('enabled');
        $azureSsoSettings->client_id = $request->input('client_id');
        $azureSsoSettings->client_secret = $request->input('client_secret');
        $azureSsoSettings->tenant_id = $request->input('tenant_id');

        $azureSsoSettings->save();

        return response()->json([
            'message' => 'Azure SSO settings updated successfully!',
        ], Response::HTTP_OK);
    }
}
