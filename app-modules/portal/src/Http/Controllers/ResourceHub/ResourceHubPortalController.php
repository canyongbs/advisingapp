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

namespace AdvisingApp\Portal\Http\Controllers\ResourceHub;

use AdvisingApp\Portal\Settings\PortalSettings;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceHubPortalController extends Controller
{
    public function assets(): JsonResponse
    {
        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/portals/resource-hub/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $portalEntry = $manifest['src/portal.js'];

        return response()->json([
            'asset_url' => route('portals.resource-hub.asset'),
            'entry' => URL::signedRoute('portals.resource-hub.api.entry'),
            'js' => route('portals.resource-hub.asset', ['file' => $portalEntry['file']]),
        ]);
    }

    public function asset(Request $request, string $file): StreamedResponse
    {
        $path = "portals/resource-hub/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }

    public function show(): JsonResponse
    {
        $settings = resolve(PortalSettings::class);

        return response()->json([
            'primary_color' => collect(Color::all()[$settings->resource_hub_portal_primary_color->value ?? 'blue'])
                ->map(Color::convertToRgb(...))
                ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
            'rounding' => $settings->resource_hub_portal_rounding?->value,
            'requires_authentication' => $settings->resource_hub_portal_requires_authentication,
            'authentication_url' => URL::signedRoute(name: 'portals.resource-hub.api.request-authentication'),
            'user_authentication_url' => route('portals.user.auth-check'),
            'access_url' => route('portal.resource-hub.show'),
            'search_url' => URL::signedRoute(name: 'portals.resource-hub.api.search'),
            'app_url' => config('app.url'),
            'api_url' => route('portals.resource-hub.api.assets'),
        ]);
    }
}
