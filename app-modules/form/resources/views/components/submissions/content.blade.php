{{--
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
--}}

@props([
    'content',
    'submission',
])

@php
    use AdvisingApp\Form\Actions\ResolveBlockRegistry;
    use AdvisingApp\Form\Actions\InjectSubmissionStateIntoTipTapContent;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
    use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

    $blocks = app(ResolveBlockRegistry::class)($submission->submissible);

    $content['content'] = app(InjectSubmissionStateIntoTipTapContent::class)($submission, $content['content'], $blocks);

    // Detect format: customBlock (RichEditor) or tiptapBlock (legacy TipTap)
    $detectBlockType = function (array $nodes) use (&$detectBlockType): ?string {
        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            $type = $node['type'] ?? null;

            if ($type === 'customBlock') {
                return 'customBlock';
            }

            if ($type === 'tiptapBlock') {
                return 'tiptapBlock';
            }

            if (! empty($node['content']) && is_array($node['content'])) {
                $found = $detectBlockType($node['content']);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    };

    $detectedType = $detectBlockType($content['content'] ?? []);
    $usesRichEditor = $detectedType === 'customBlock';
    $usesLegacyTipTap = $detectedType === 'tiptapBlock';

    $sanitizeSubmissionHtml = function (string $html): string {
        $config = app(HtmlSanitizerConfig::class)
            ->allowElement('svg', ['xmlns', 'fill', 'viewBox', 'stroke-width', 'stroke', 'aria-hidden', 'class', 'data-slot'])
            ->allowElement('path', ['fill-rule', 'clip-rule', 'd', 'stroke-linecap', 'stroke-linejoin']);

        return (new HtmlSanitizer($config))->sanitize($html);
    };
@endphp

<div class="prose max-w-none dark:prose-invert">
    @if (! empty($content['content']))
        @if ((! $usesRichEditor) && $usesLegacyTipTap)
            {{-- Legacy TipTap format (Events, Case Forms — until migrated to RichEditor) --}}
            {!! tiptap_converter()->blocks($blocks)->asHTML($content) !!}
        @else
            {!! $sanitizeSubmissionHtml(RichContentRenderer::make($content)->customBlocks(array_values($blocks))->toUnsafeHtml()) !!}
        @endif
    @else
        This submission has no content.
    @endif
</div>
