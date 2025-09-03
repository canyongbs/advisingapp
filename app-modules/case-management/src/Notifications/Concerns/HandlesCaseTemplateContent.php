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

namespace AdvisingApp\CaseManagement\Notifications\Concerns;

use AdvisingApp\CaseManagement\Actions\GenerateCaseTypeEmailTemplateContent;
use AdvisingApp\CaseManagement\Actions\GenerateCaseTypeEmailTemplateSubject;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use App\Features\AssignedToMergeTagRenameFeatureFlag;
use Illuminate\Support\HtmlString;

trait HandlesCaseTemplateContent
{
    /**
     * @param string|array<string, mixed> $body
     * @param ?CaseTypeEmailTemplateRole $urlType
     */
    public function getBody($body, ?CaseTypeEmailTemplateRole $urlType = null): HtmlString
    {
        if (is_array($body)) {
            $body = $this->injectButtonUrlIntoTiptapContent($body, $urlType);
        }

        return app(GenerateCaseTypeEmailTemplateContent::class)(
            $body,
            $this->getMergeData(),
            $this->case,
            'body',
        );
    }

    /**
     * @param string|array<string, mixed> $subject
     */
    public function getSubject($subject): HtmlString
    {
        return app(GenerateCaseTypeEmailTemplateSubject::class)(
            $subject,
            $this->getMergeData(),
            $this->case,
            'subject',
        );
    }

    /**
     * @return array<string, string>
     */
    public function getMergeData(): array
    {
        $assignedToKey = AssignedToMergeTagRenameFeatureFlag::active() ? 'assigned staff name' : 'assigned to';

        return [
            'case number' => $this->case->case_number,
            'created date' => $this->case->created_at->format('d-m-Y H:i'),
            'updated date' => $this->case->updated_at->format('d-m-Y H:i'),
            ...[$assignedToKey => $this->case->assignedTo->user->name ?? 'Unassigned'],
            'status' => $this->case->status->name,
            'type' => $this->case->priority->type->name,
            'description' => $this->case->close_details,
        ];

        // @todo AssignedToMergeTagRenameFeatureFlag:
        // Once this feature flag is removed, delete the $assignedToKey line and the current return block.
        // Then uncomment and use the return block below.

        // return [
        //     'case number' => $this->case->case_number,
        //     'created date' => $this->case->created_at->format('d-m-Y H:i'),
        //     'updated date' => $this->case->updated_at->format('d-m-Y H:i'),
        //     'assigned staff name' => $this->case->assignedTo->user->name ?? 'Unassigned',
        //     'status' => $this->case->status->name,
        //     'type' => $this->case->priority->type->name,
        //     'description' => $this->case->close_details,
        // ];
    }

    /**
     * @param array<string, mixed> $content
     * @param ?CaseTypeEmailTemplateRole $urlType
     *
     * @return array<string, mixed>
     */
    protected function injectButtonUrlIntoTiptapContent(array $content, ?CaseTypeEmailTemplateRole $urlType = null): array
    {
        if (! isset($content['content']) || ! is_array($content['content'])) {
            return $content;
        }

        $content['content'] = array_map(function (mixed $block) use ($urlType) {
            if (
                $block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'caseTypeEmailTemplateButtonBlock'
            ) {
                $block['attrs']['data']['url'] = $urlType == CaseTypeEmailTemplateRole::Customer ? route('portal.case.show', $this->case) : CaseResource::getUrl('view', [
                    'record' => $this->case,
                ]);
            }

            if (
                $block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'surveyResponseEmailTemplateTakeSurveyButtonBlock'
            ) {
                $block['attrs']['data']['url'] = route('feedback.case', $this->case);
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $block = $this->injectButtonUrlIntoTiptapContent($block);
            }

            return $block;
        }, $content['content']);

        return $content;
    }
}
