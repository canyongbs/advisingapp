{{--
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
--}}
@php
    use AdvisingApp\Campaign\Settings\CampaignSettings;
    use AdvisingApp\Notification\Enums\NotificationChannel;
    use AdvisingApp\Engagement\Models\EngagementBatch;
    use Carbon\Carbon;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

    // During campaign creation the body's images are pending uploads held on the Livewire
    // component (not yet persisted as media), so RichContentRenderer cannot resolve them by id.
    // Inject each pending upload's temporary URL into its image node and clear the id, which
    // makes RichContentRenderer keep that src instead of overwriting it with a null lookup.
    $resolvePendingImages = function (mixed $content, array $pendingAttachments) use (&$resolvePendingImages): mixed {
        if (! is_array($content) || empty($content['content']) || ! is_array($content['content'])) {
            return $content;
        }

        foreach ($content['content'] as $index => $node) {
            if (! is_array($node)) {
                continue;
            }

            if (($node['type'] ?? null) === 'image') {
                $file = ($id = $node['attrs']['id'] ?? null) !== null ? $pendingAttachments[$id] ?? null : null;

                if ($file instanceof TemporaryUploadedFile) {
                    try {
                        $content['content'][$index]['attrs']['src'] = $file->temporaryUrl();
                        $content['content'][$index]['attrs']['id'] = null;
                    } catch (Throwable $exception) {
                        // Not previewable (e.g. a non-image attachment); leave the node untouched.
                    }
                }

                continue;
            }

            $content['content'][$index] = $resolvePendingImages($node, $pendingAttachments);
        }

        return $content;
    };

    $bodyContent = $action['body'];

    if (is_string($bodyContent)) {
        $bodyContent = json_decode($bodyContent, associative: true) ?? $bodyContent;
    }

    $bodyContent = $resolvePendingImages($bodyContent, $this->componentFileAttachments['data']['actions'][$actionIndex]['data']['body'] ?? []);
@endphp

<x-filament::fieldset>
    <x-slot name="label">
        @if ($action['channel'] === NotificationChannel::Email->value)
            Email
        @else
            Text Message
        @endif
    </x-slot>

    <dl class="max-w-md divide-y divide-gray-200 text-gray-900 dark:divide-gray-700 dark:text-white">
        <div class="flex flex-col pb-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Delivery Method</dt>
            <dd class="flex flex-row space-x-2 text-sm font-semibold">
                <x-filament::badge>
                    {{ $action['channel'] }}
                </x-filament::badge>
            </dd>
        </div>
        @if (isset($action['subject']))
            <div class="flex flex-col pt-3">
                <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Subject</dt>
                <dd class="text-sm font-semibold">
                    {!! EngagementBatch::renderWithMergeTags(RichContentRenderer::make($action['subject'])->toHtml()) !!}
                </dd>
            </div>
        @endif

        @if ($action['body'])
            <div class="flex flex-col pt-3">
                <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Body</dt>
                <dd
                    class="prose dark:prose-invert prose-h1:my-4 prose-h1:text-3xl prose-h1:font-bold prose-h2:my-4 prose-h2:text-2xl prose-h3:my-4 prose-h3:text-xl prose-h4:my-4 prose-h4:text-lg prose-h5:my-4 prose-h5:text-base prose-h5:font-medium prose-h6:my-4 prose-h6:text-sm prose-h6:font-medium prose-hr:my-4"
                >
                    {!! EngagementBatch::renderWithMergeTags( RichContentRenderer::make($bodyContent)->fileAttachmentsDisk('s3-public')->fileAttachmentsVisibility('public')->toHtml(),) !!}
                </dd>
            </div>
        @endif

        <div class="flex flex-col pt-3">
            <dt class="mb-1 text-sm text-gray-500 dark:text-gray-400">Execute At</dt>
            <dd class="text-sm font-semibold">
                {{ Carbon::parse($action['execute_at'])->format('M j, Y g:i a (T)') }}
                {{ app(CampaignSettings::class)->getActionExecutionTimezoneLabel() }}
            </dd>
        </div>
    </dl>
</x-filament::fieldset>
