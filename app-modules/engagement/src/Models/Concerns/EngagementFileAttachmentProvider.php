<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Models\Concerns;

use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Spatie\MediaLibrary\HasMedia;
use Throwable;

class EngagementFileAttachmentProvider extends SpatieMediaLibraryFileAttachmentProvider
{
    public function getFileAttachmentUrl(mixed $file): ?string
    {
        $url = parent::getFileAttachmentUrl($file);

        if ($url) {
            return $url;
        }

        $model = $this->attribute->getModel();

        if (! $model instanceof Engagement) {
            return null;
        }

        $source = $model->source;

        if (! $source instanceof HasMedia) {
            return null;
        }

        $collection = $this->getSourceMediaCollection($source);

        if (! $collection) {
            return null;
        }

        $media = $source->getMedia($collection)->keyBy('uuid');

        if (! $media->has($file)) {
            return null;
        }

        $fileAttachment = $media->get($file);

        if ($this->attribute->getFileAttachmentsVisibility() === 'private') {
            try {
                return $fileAttachment->getTemporaryUrl(
                    now()->addMinutes(30)->endOfHour(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $fileAttachment->getUrl();
    }

    protected function getSourceMediaCollection(HasMedia $source): ?string
    {
        if ($source instanceof HasRichContent) {
            if ($source instanceof CampaignAction || $source instanceof WorkflowEngagementEmailDetails) {
                return 'body';
            }
        }

        return null;
    }
}
