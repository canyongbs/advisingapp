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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Models;

use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\Contracts\ExecutableFromACampaignAction;
use AdvisingApp\Engagement\Actions\CreateEngagementBatch;
use AdvisingApp\Engagement\DataTransferObjects\EngagementBatchCreationData;
use AdvisingApp\Engagement\Models\Concerns\HasManyEngagements;
use AdvisingApp\Engagement\Observers\EngagementBatchObserver;
use App\Models\BaseModel;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Throwable;

/**
 * @mixin IdeHelperEngagementBatch
 */
#[ObservedBy([EngagementBatchObserver::class])]
class EngagementBatch extends BaseModel implements ExecutableFromACampaignAction, HasMedia
{
    use HasManyEngagements;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function executeFromCampaignAction(CampaignAction $action): bool|string
    {
        try {
            CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
                'user' => $action->campaign->createdBy,
                'records' => $action->campaign->segment->retrieveRecords()->filter(function ($record) {
                    return $record->canRecieveSms();
                }),
                'channel' => $action->data['channel'],
                'subject' => $action->data['subject'] ?? null,
                'body' => $action->data['body'] ?? null,
            ]));

            return true;
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        // Do we need to be able to relate campaigns/actions to the RESULT of their actions?
    }

    public static function renderWithMergeTags(string $html): string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);

        $spans = $xpath->query("//span[@data-type='mergeTag']");

        foreach ($spans as $span) {
            $dataId = $span->getAttribute('data-id');
            $span->nodeValue = "{{ {$dataId} }}";
        }

        return $dom->saveHTML();
    }
}
