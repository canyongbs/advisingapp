<?php

namespace AdvisingApp\Theme\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use AdvisingApp\Theme\Settings\ThemeSettings;
use AdvisingApp\Theme\Http\Requests\BrandedWebsiteLinksRequest;

class BrandedWebsiteLinksController extends Controller
{
    public function __invoke(BrandedWebsiteLinksRequest $request): JsonResponse
    {
        $tenant = Tenant::findOrFail($request->validate('tenant_id'));
        \Log::debug('BrandedWebsiteLinksController >> invoke >> in');
        $tenant->execute(function () use ($request) {
            $settings = app(ThemeSettings::class);
            $settings->is_support_url_enabled = $request->validated('is_support_url_enabled');
            $settings->support_url = $request->validated('support_url');
            $settings->is_recent_updates_url_enabled = $request->validated('is_recent_updates_url_enabled');
            $settings->recent_updates_url = $request->validated('recent_updates_url');
            $settings->is_custom_link_url_enabled = $request->validated('is_custom_link_url_enabled');
            $settings->custom_link_label = $request->validated('custom_link_label');
            $settings->custom_link_url = $request->validated('custom_link_url');
            $settings->tenant_id = $request->validated('tenant_id');
            $settings->save();
        });

        return response()->json([
            'message' => 'Theme updated successfully!',
        ]);
    }
}
