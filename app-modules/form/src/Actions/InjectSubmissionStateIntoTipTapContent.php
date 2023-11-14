<?php

namespace Assist\Form\Actions;

use Assist\Form\Models\FormField;
use Assist\Form\Models\FormSubmission;

class InjectSubmissionStateIntoTipTapContent
{
    public function __invoke(FormSubmission $submission, array $content): array
    {
        foreach ($content as $componentKey => $component) {
            if (! is_array($component)) {
                continue;
            }

            if (array_key_exists('content', $component)) {
                $content[$componentKey]['content'] = $this($submission, $component['content']);

                continue;
            }

            if (($component['type'] ?? null) !== 'tiptapBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];

            if (blank($componentAttributes['id'] ?? null)) {
                continue;
            }

            $content[$componentKey]['attrs']['data']['response'] = $submission->fields
                ->first(fn (FormField $field): bool => $field->id === $componentAttributes['id'])
                ?->pivot->response;
        }

        return $content;
    }
}
