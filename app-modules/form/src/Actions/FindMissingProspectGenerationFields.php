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

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Enums\ProspectGenerationField;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;

/**
 * Finds which fields a prospect-generating form is missing: a required mapped email block and a mapped name block.
 */
class FindMissingProspectGenerationFields
{
    /**
     * @param iterable<mixed> $documents Rich content documents (a single page's content, or each step's content), as arrays or JSON strings. Fields may be spread across documents.
     *
     * @return list<ProspectGenerationField> The missing fields, email first. Empty when nothing is missing.
     */
    public function __invoke(iterable $documents): array
    {
        $hasRequiredEmail = false;
        $hasName = false;

        foreach ($documents as $document) {
            if (is_string($document)) {
                $document = json_decode($document, true);
            }

            if (! is_array($document) || ! isset($document['content']) || ! is_array($document['content'])) {
                continue;
            }

            $this->searchNodes($document['content'], $hasRequiredEmail, $hasName);
        }

        $missingFields = [];

        if (! $hasRequiredEmail) {
            $missingFields[] = ProspectGenerationField::Email;
        }

        if (! $hasName) {
            $missingFields[] = ProspectGenerationField::Name;
        }

        return $missingFields;
    }

    /**
     * @param array<mixed> $nodes
     */
    private function searchNodes(array $nodes, bool &$hasRequiredEmail, bool &$hasName): void
    {
        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            if (($node['type'] ?? null) === 'customBlock') {
                $blockType = $node['attrs']['id'] ?? null;
                $blockConfig = $node['attrs']['config'] ?? [];

                if (($blockType === EducatableEmailFormFieldBlock::type()) && ($blockConfig['isRequired'] ?? $blockConfig['is_required'] ?? false)) {
                    $hasRequiredEmail = true;
                }

                if ($blockType === EducatableNameFormFieldBlock::type()) {
                    $hasName = true;
                }
            }

            if (isset($node['content']) && is_array($node['content'])) {
                $this->searchNodes($node['content'], $hasRequiredEmail, $hasName);
            }
        }
    }
}
