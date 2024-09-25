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
        $data = $request->validated();

        $tenant = Tenant::findOrFail($data['tenant_id']);

        $tenant->execute(function () use ($data) {
            $settings = app(ThemeSettings::class);
            $settings->is_support_url_enabled = $data['is_support_url_enabled'];
            $settings->support_url = $data['support_url'];
            $settings->is_recent_updates_url_enabled = $data['is_recent_updates_url_enabled'];
            $settings->recent_updates_url = $data['recent_updates_url'];
            $settings->is_custom_link_url_enabled = $data['is_custom_link_url_enabled'];
            $settings->custom_link_label = $data['custom_link_label'];
            $settings->custom_link_url = $data['custom_link_url'];
            $settings->tenant_id = $data['tenant_id'];
            $settings->save();
        });

        return response()->json([
            'message' => 'Theme updated successfully!',
        ]);
    }
}
