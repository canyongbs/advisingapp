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

namespace AdvisingApp\CaseManagement\Notifications\Concerns;

use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Filament\Blocks\CaseTypeEmailTemplateButtonBlock;
use AdvisingApp\CaseManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\CaseResource;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait HandlesCaseTemplateContent
{
    /**
     * @param string|array<string, mixed> $body
     */
    public function getBody($body, ?CaseTypeEmailTemplateRole $urlType = null): HtmlString
    {
        if (is_array($body)) {
            $body = $this->injectButtonUrlIntoBlocks($body, $urlType);
        }

        $html = RichContentRenderer::make($body)
            ->customBlocks([
                CaseTypeEmailTemplateButtonBlock::class,
                SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
            ])
            ->mergeTags($this->getMergeData())
            ->toHtml();

        return str($html)->sanitizeHtml()->toHtmlString();
    }

    /**
     * @param string|array<string, mixed> $subject
     */
    public function getSubject($subject): HtmlString
    {
        $html = RichContentRenderer::make($subject)
            ->mergeTags($this->getMergeData())
            ->toHtml();

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        $text = Str::limit($text, 988, '');

        return new HtmlString($text);
    }

    /**
     * @return array<string, string>
     */
    public function getMergeData(): array
    {
        return [
            'contact name' => $this->case->respondent->{$this->case->respondent::displayNameKey()},
            'case number' => $this->case->case_number,
            'created date' => $this->case->created_at->format('M j, Y g:i a (T)'),
            'updated date' => $this->case->updated_at->format('M j, Y g:i a (T)'),
            'assigned staff name' => $this->case->assignedTo->user->name ?? 'Unassigned',
            'status' => $this->case->status->name,
            'type' => $this->case->priority->type->name,
            'description' => $this->case->close_details,
        ];
    }

    /**
     * @param array<string, mixed> $content
     *
     * @return array<string, mixed>
     */
    protected function injectButtonUrlIntoBlocks(array $content, ?CaseTypeEmailTemplateRole $urlType = null): array
    {
        if (! isset($content['content']) || ! is_array($content['content'])) {
            return $content;
        }

        $content['content'] = array_map(function (mixed $block) use ($urlType) {
            if (! is_array($block)) {
                return $block;
            }

            $blockId = $this->getBlockId($block);

            if ($blockId === 'caseTypeEmailTemplateButtonBlock') {
                $url = $urlType === CaseTypeEmailTemplateRole::Customer
                    ? null
                    // This can be restored if/when we add case management to the portal
                    // ? route('portal.case.show', $this->case)
                    : CaseResource::getUrl('view', [
                        'record' => $this->case,
                    ]);

                $block = $this->setBlockConfigUrl($block, $url);
            }

            if ($blockId === 'surveyResponseEmailTemplateTakeSurveyButtonBlock') {
                $block = $this->setBlockConfigUrl($block, route('feedback.case', $this->case));
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $block = $this->injectButtonUrlIntoBlocks($block);
            }

            return $block;
        }, $content['content']);

        return $content;
    }

    /**
     * @param array<string, mixed> $block
     */
    protected function getBlockId(array $block): ?string
    {
        return match ($block['type'] ?? null) {
            'customBlock' => $block['attrs']['id'] ?? null,
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $block
     *
     * @return array<string, mixed>
     */
    protected function setBlockConfigUrl(array $block, ?string $url): array
    {
        if (($block['type'] ?? null) === 'customBlock') {
            $block['attrs']['config']['url'] = $url;
        }

        return $block;
    }
}
