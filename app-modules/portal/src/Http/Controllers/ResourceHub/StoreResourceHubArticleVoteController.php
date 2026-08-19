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

namespace AdvisingApp\Portal\Http\Controllers\ResourceHub;

use AdvisingApp\Portal\Actions\CalculateResourceHubArticleHelpfulVotePercentage;
use AdvisingApp\Portal\Actions\ResolveResourceHubPortalVoter;
use AdvisingApp\Portal\Http\Requests\StoreResourceHubArticleVoteRequest;
use AdvisingApp\Portal\Models\ResourceHubArticleVote;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use App\Features\ResourceHubArticleFeedbackFeature;
use App\Http\Controllers\Controller;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;

class StoreResourceHubArticleVoteController extends Controller
{
    public function __invoke(StoreResourceHubArticleVoteRequest $request): JsonResponse
    {
        // Before the feature's migration has run for this tenant, vote table doesn't exist yet.
        if (! ResourceHubArticleFeedbackFeature::active()) {
            return response()->json([
                'is_helpful' => null,
                'helpful_vote_percentage' => 0,
            ]);
        }

        $voter = ResolveResourceHubPortalVoter::execute();

        $vote = ResourceHubArticleVote::query()
            ->where('article_id', $request->article_id)
            ->where('voter_id', $voter->getKey())
            ->where('voter_type', $voter->getMorphClass())
            ->first();

        if (! is_null($request->article_vote)) {
            $vote ??= new ResourceHubArticleVote();
            $vote->voter()->associate($voter);
            $vote->article_id = $request->article_id;
            $vote->is_helpful = $request->article_vote;

            try {
                $vote->save();
            } catch (UniqueConstraintViolationException) {
                // A concurrent request already inserted this voter's vote; fall back to updating it.
                $vote = ResourceHubArticleVote::query()
                    ->where('article_id', $request->article_id)
                    ->where('voter_id', $voter->getKey())
                    ->where('voter_type', $voter->getMorphClass())
                    ->firstOrFail();
                $vote->update(['is_helpful' => $request->article_vote]);
            }
        } elseif ($vote) {
            $vote->delete();
            $vote = null;
        }

        $article = ResourceHubArticle::find($request->article_id);

        $helpfulVotePercentage = $article ? CalculateResourceHubArticleHelpfulVotePercentage::execute($article) : 0;

        return response()->json([
            'is_helpful' => $vote?->is_helpful,
            'helpful_vote_percentage' => $helpfulVotePercentage,
        ]);
    }
}
