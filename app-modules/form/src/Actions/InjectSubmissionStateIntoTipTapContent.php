<?php

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
