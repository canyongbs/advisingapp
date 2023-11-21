<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Form\Actions;

use Assist\Form\Models\FormField;
use Assist\Form\Models\FormSubmission;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;

class InjectSubmissionStateIntoTipTapContent
{
    public function __invoke(FormSubmission $submission, array $content, ?array $blocks = null): array
    {
        $blocks ??= FormFieldBlockRegistry::keyByType();

        foreach ($content as $componentKey => $component) {
            if (! is_array($component)) {
                continue;
            }

            if (array_key_exists('content', $component)) {
                $content[$componentKey]['content'] = $this($submission, $component['content'], $blocks);

                continue;
            }

            if (($component['type'] ?? null) !== 'tiptapBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];

            if (blank($componentAttributes['id'] ?? null)) {
                continue;
            }

            $block = $blocks[$componentAttributes['type']] ?? null;

            if (blank($block)) {
                continue;
            }

            $field = $submission->fields
                ->first(fn (FormField $field): bool => $field->id === $componentAttributes['id']);

            if (! $field) {
                continue;
            }

            $content[$componentKey]['attrs']['data'] = [
                ...$content[$componentKey]['attrs']['data'],
                ...$block::getSubmissionState($field->pivot->response),
            ];
        }

        return $content;
    }
}
