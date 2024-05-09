<?php

namespace AdvisingApp\Survey\Actions;

use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveyStep;
use AdvisingApp\Survey\Models\SurveyField;

class DuplicateSurvey
{
    public function __construct(
        private Survey $original,
        private Survey $replica
    ) {}

    public function __invoke(): void
    {
        $stepMap = $this->replicateSteps();
        $fieldMap = $this->replicateFields($stepMap);
        $this->updateStepContent($fieldMap);
    }

    protected function replicateSteps(): array
    {
        $stepMap = [];

        $this->original->steps()->each(function (SurveyStep $step) use (&$stepMap) {
            $newStep = $step->replicate();
            $newStep->survey_id = $this->replica->id;
            $newStep->save();

            $stepMap[$step->id] = $newStep->id;
        });

        return $stepMap;
    }

    protected function replicateFields(array $stepMap): array
    {
        $fieldMap = [];

        $this->original->fields()->each(function (SurveyField $field) use (&$fieldMap, $stepMap) {
            $newField = $field->replicate();
            $newField->survey_id = $this->replica->id;
            $newField->step_id = $stepMap[$field->step_id] ?? null;
            $newField->save();

            $fieldMap[$field->id] = $newField->id;
        });

        return $fieldMap;
    }

    protected function updateStepContent(array $fieldMap): void
    {
        $this->replica->steps()->each(function (SurveyStep $step) use ($fieldMap) {
            $content = $step->content;

            $step->update([
                'content' => $this->replaceIdsInContent($content, $fieldMap),
            ]);
        });
    }

    protected function replaceIdsInContent(&$content, $fieldMap)
    {
        if (is_array($content)) {
            foreach ($content as $key => &$value) {
                if (is_array($value)) {
                    $this->replaceIdsInContent($value, $fieldMap);
                } else {
                    if ($key === 'id' && isset($fieldMap[$value])) {
                        $value = $fieldMap[$value];
                    }
                }
            }
        }

        return $content;
    }
}
