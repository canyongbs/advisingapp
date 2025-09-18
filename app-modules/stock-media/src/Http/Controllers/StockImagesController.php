<?php

namespace AdvisingApp\StockMedia\Http\Controllers;

use AdvisingApp\StockMedia\Enums\StockMediaProvider;
use AdvisingApp\StockMedia\Settings\StockMediaSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class StockImagesController
{
    public function __invoke(): JsonResponse
    {
        $settings = app(StockMediaSettings::class);

        if (! $settings->is_active) {
            abort(404);
        }

        if ($settings->provider !== StockMediaProvider::Pexels) {
            abort(404);
        }

        if (blank($settings->pexels_api_key)) {
            abort(404);
        }

        $response = Http::withHeader('Authorization', $settings->pexels_api_key)
            ->get('https://api.pexels.com/v1/search', query: [
                'query' => request()->get('search'),
                'per_page' => 15,
                'page' => request()->get('page') ?? 1,
            ]);

        if (! $response->successful()) {
            abort(500, 'Failed to fetch images from Pexels.');
        }

        $data = $response->json();

        return response()->json([
            'data' => array_map(fn (array $image): array => [
                'url' => $image['src']['large2x'],
                'preview_url' => $image['src']['tiny'],
                'title' => $image['alt'],
            ], $data['photos'] ?? []),
            'current_page' => $data['page'] ?? 1,
            'last_page' => (int) ceil(($data['total_results'] ?? 0) / 15),
            'total' => $data['total_results'] ?? 0,
        ]);
    }
}
