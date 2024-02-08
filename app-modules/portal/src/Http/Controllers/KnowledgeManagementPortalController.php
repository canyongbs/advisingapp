<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Portal\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Scopes\SearchBy;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use App\Http\Controllers\Controller;
use AdvisingApp\Portal\Settings\PortalSettings;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AdvisingApp\Portal\DataTransferObjects\KnowledgeBaseItemData;
use AdvisingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;
use AdvisingApp\Portal\DataTransferObjects\KnowledgeManagementSearchData;

class KnowledgeManagementPortalController extends Controller
{
    public function view(): JsonResponse
    {
        $settings = resolve(PortalSettings::class);

        // TODO We potentially want to generate the API key here

        return response()->json([
            'primary_color' => Color::all()[$settings->knowledge_management_portal_primary_color ?? 'blue'],
            'rounding' => $settings->knowledge_management_portal_rounding,
            'categories' => KnowledgeBaseCategoryData::collection(
                KnowledgeBaseCategory::query()
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->getKey(),
                            'name' => $category->name,
                            'description' => $category->description,
                        ];
                    })
                    ->toArray()
            ),
        ]);
    }

    // TODO Extract to dedicated controller
    public function search(Request $request)
    {
        ray('request', $request->all());

        $itemData = KnowledgeBaseItemData::collection(
            KnowledgeBaseItem::query()
                ->public()
                ->tap(new SearchBy('title', Str::lower($request->get('search'))))
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->getKey(),
                        'name' => $item->title,
                    ];
                })
                ->toArray()
        );

        $categoryData = KnowledgeBaseCategoryData::collection(
            KnowledgeBaseCategory::query()
                ->tap(new SearchBy('name', Str::lower($request->get('search'))))
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->getKey(),
                        'name' => $category->name,
                        'description' => $category->description,
                    ];
                })
                ->toArray()
        );

        $searchResults = KnowledgeManagementSearchData::from([
            'articles' => $itemData,
            'categories' => $categoryData,
        ]);

        ray('searchResults');

        return $searchResults->wrap('data');
    }

    // TODO Extract to dedicated controller
    public function category(KnowledgeBaseCategory $category)
    {
        return response()->json([
            'category' => KnowledgeBaseCategoryData::from([
                'id' => $category->getKey(),
                'name' => $category->name,
                'description' => $category->description,
            ]),
            'articles' => KnowledgeBaseItemData::collection(
                $category->knowledgeBaseItems()
                    ->public()
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->getKey(),
                            'name' => $item->title,
                        ];
                    })
                    ->toArray()
            ),
        ]);
    }

    // TODO Extract to dedicated controller
    public function article(KnowledgeBaseCategory $category, KnowledgeBaseItem $article)
    {
        return response()->json([
            'category' => KnowledgeBaseCategoryData::from([
                'id' => $category->getKey(),
                'name' => $category->name,
                'description' => $category->description,
            ]),
            'article' => KnowledgeBaseItemData::from([
                'id' => $article->getKey(),
                'name' => $article->title,
                'lastUpdated' => $article->updated_at->format('M d Y, h:m a'),
                'content' => tiptap_converter()->asHTML($article->article_details),
            ]),
        ]);
    }
}
