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

@props([
    'content',
    'submission',
])

@php
    use AdvisingApp\Form\Actions\ResolveBlockRegistry;
    use AdvisingApp\Form\Actions\InjectSubmissionStateIntoContent;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
    use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

    $blocks = app(ResolveBlockRegistry::class)($submission->submissible);

    $content['content'] = app(InjectSubmissionStateIntoContent::class)($submission, $content['content'], $blocks);

    $sanitizeSubmissionHtml = function (string $html): string {
        $config = app(HtmlSanitizerConfig::class)
            ->allowElement('svg', ['xmlns', 'fill', 'viewBox', 'stroke-width', 'stroke', 'aria-hidden', 'class', 'data-slot'])
            ->allowElement('path', ['fill-rule', 'clip-rule', 'd', 'stroke-linecap', 'stroke-linejoin'])
            ->allowAttribute('data-secret-row', 'div')
            ->allowAttribute('data-secret-mask', 'span')
            ->allowAttribute('data-secret-value', 'code')
            ->allowAttribute('data-secret-error', 'span')
            ->allowAttribute('data-secret-reveal', 'button')
            ->allowAttribute('data-secret-id', 'button');

        return (new HtmlSanitizer($config))->sanitize($html);
    };
@endphp

<div
    class="fi-prose"
    x-data="{
        async revealSecret(event) {
            const button = event.target.closest('[data-secret-reveal]')

            if (! button || button.disabled) {
                return
            }

            const row = button.closest('[data-secret-row]')
            const mask = row?.querySelector('[data-secret-mask]')
            const value = row?.querySelector('[data-secret-value]')
            const error = row?.querySelector('[data-secret-error]')
            const originalLabel = button.textContent

            button.disabled = true
            button.textContent = 'Revealing...'
            error?.classList.add('hidden')

            try {
                const response = await fetch(@js(route('submission-secrets.reveal')), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @js(csrf_token()),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ secret_id: button.dataset.secretId }),
                })

                if (! response.ok) {
                    throw new Error('Unable to reveal password.')
                }

                const data = await response.json()

                value.textContent = data.value
                mask.classList.add('hidden')
                value.classList.remove('hidden')
                button.classList.add('hidden')
            } catch {
                error.textContent = 'The password could not be revealed.'
                error.classList.remove('hidden')
            } finally {
                button.disabled = false
                button.textContent = originalLabel
            }
        },
    }"
    x-on:click="revealSecret($event)"
>
    @if (! empty($content['content']))
        {!! $sanitizeSubmissionHtml( RichContentRenderer::make($content)->customBlocks(array_values($blocks))->toUnsafeHtml(),) !!}
    @else
        This submission has no content.
    @endif
</div>
