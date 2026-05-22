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

namespace AdvisingApp\StudentDataModel\Filament\Actions;

use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class AiSortEnrollmentSemestersAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('AI Sort')
            ->icon('heroicon-m-sparkles')
            ->modalHeading('AI-Powered Semester Sort')
            ->modalDescription('This feature will attempt to sort all semester records into the most likely chronological descending order based on their names, codes, years, and term indicators. Because semester codes vary by institution, the result may not be perfect. You can provide specific directions below to help guide the sort.')
            ->modalSubmitActionLabel('Sort')
            ->schema([
                Textarea::make('instructions')
                    ->label('Additional sorting instructions')
                    ->rows(3)
                    ->placeholder('Example: Treat Winter as part of the following Spring term. Sort Fall before Spring within the same academic year.')
                    ->maxLength(1000),
            ])
            ->action(function (array $data) {
                $semesters = EnrollmentSemester::query()
                    ->orderBy('order')
                    ->get(['id', 'name', 'order']);

                if ($semesters->isEmpty()) {
                    Notification::make()
                        ->title('No Semesters')
                        ->body('There are no semesters to sort.')
                        ->warning()
                        ->send();

                    return;
                }

                $idMap = [];
                $semesterRecords = $semesters->values()->map(function (EnrollmentSemester $semester, int $index) use (&$idMap) {
                    $numericId = $index + 1;
                    $idMap[$numericId] = $semester->id;

                    return [
                        'id' => $numericId,
                        'name' => $semester->name,
                        'current_sort_order' => $semester->order,
                    ];
                })->all();

                $content = json_encode([
                    'instructions' => $data['instructions'] ?? '',
                    'semesters' => $semesterRecords,
                ], JSON_PRETTY_PRINT);

                $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();

                try {
                    $completion = app(CompletePrompt::class)->execute(
                        aiModel: $model,
                        prompt: $this->getAiPrompt(),
                        content: $content,
                    );
                } catch (MessageResponseException $exception) {
                    report($exception);

                    Notification::make()
                        ->title('AI Sort Error')
                        ->body('There was an issue communicating with the AI assistant. Please try again later.')
                        ->danger()
                        ->send();

                    $this->halt();

                    return;
                }

                $jsonString = trim($completion);

                if (str_starts_with($jsonString, '```')) {
                    $jsonString = preg_replace('/^```(?:json)?\s*/', '', $jsonString);
                    $jsonString = preg_replace('/\s*```$/', '', $jsonString);
                }

                $decoded = json_decode($jsonString, true);

                if (! is_array($decoded) || ! array_key_exists('ordered_semester_ids', $decoded)) {
                    Notification::make()
                        ->title('AI Sort Failed')
                        ->body('The AI returned an invalid response format. Order was not changed.')
                        ->danger()
                        ->send();

                    return;
                }

                $orderedIds = collect((array) $decoded['ordered_semester_ids'])
                    ->map(fn ($id) => (int) $id);
                $originalNumericIds = collect(array_keys($idMap));

                if ($orderedIds->count() !== $originalNumericIds->count()) {
                    Notification::make()
                        ->title('AI Sort Failed')
                        ->body('The AI returned a different number of semesters than expected. Order was not changed.')
                        ->danger()
                        ->send();

                    return;
                }

                if ($orderedIds->duplicates()->isNotEmpty()) {
                    Notification::make()
                        ->title('AI Sort Failed')
                        ->body('The AI returned duplicate semester IDs. Order was not changed.')
                        ->danger()
                        ->send();

                    return;
                }

                $unknownIds = $orderedIds->diff($originalNumericIds);

                if ($unknownIds->isNotEmpty()) {
                    Notification::make()
                        ->title('AI Sort Failed')
                        ->body('The AI returned semester IDs that do not exist. Order was not changed.')
                        ->danger()
                        ->send();

                    return;
                }

                $missingIds = $originalNumericIds->diff($orderedIds);

                if ($missingIds->isNotEmpty()) {
                    Notification::make()
                        ->title('AI Sort Failed')
                        ->body('The AI response is missing some semester IDs. Order was not changed.')
                        ->danger()
                        ->send();

                    return;
                }

                DB::transaction(function () use ($orderedIds, $idMap) {
                    $orderedIds->each(function (int $numericId, int $index) use ($idMap) {
                        EnrollmentSemester::query()
                            ->where('id', $idMap[$numericId])
                            ->update(['order' => $index + 1]);
                    });
                });

                Notification::make()
                    ->title('Semesters Sorted')
                    ->body('The semester order has been updated based on AI analysis.')
                    ->success()
                    ->send();
            })
            ->visible(
                auth()->user()->hasLicense(LicenseType::ConversationalAi)
            );
    }

    public static function getDefaultName(): ?string
    {
        return 'aiSort';
    }

    protected function getAiPrompt(): string
    {
        return <<<'EOL'
        You are sorting semester records for a college CRM.

        Your task is to return the most likely chronological order for the provided semester records.

        Semester names and codes may come from a Student Information System and may be inconsistent. They may include years, two-digit years, academic years, terms, abbreviations, test values, legacy values, or non-standard labels.

        General ordering guidance:
        - Sort primarily by year or academic year.
        - Within the same year or academic year, use the most likely term order.
        - Unless the user provides different instructions, use this term order:
          Fall, Winter, Spring, Summer.
        - Some institutions may not use every term.
        - Some institutions may have multiple sessions within one term, such as Summer 1, Summer 2, Spring A, Spring B, Mini, First 8 Week, Second 8 Week.
        - When multiple sessions exist within the same term, sort the main term first when clear, then shorter or numbered sessions in natural order.
        - Ambiguous records must still be included.
        - Test, legacy, view, edit, delete, placeholder, or unusual values must not be removed unless the user explicitly instructs you to exclude them. Even then, you must still return every ID somewhere in the ordered list.
        - Never invent, remove, rename, or modify semester records.
        - Preserve all submitted IDs.
        - Return every submitted semester ID exactly once.
        - Do not return IDs that were not submitted.

        Return only valid JSON in this exact format:

        {
          "ordered_semester_ids": [
            123,
            124,
            125
          ],
          "notes": "Briefly explain any major assumptions, especially for ambiguous values."
        }

        Important output rules:
        - The ordered_semester_ids array must contain every submitted semester ID exactly once.
        - The ordered_semester_ids array must not contain duplicate IDs.
        - The ordered_semester_ids array must not contain unknown IDs.
        - The response must be valid JSON only.
        - Do not include markdown.
        - Do not include prose outside the JSON object.
        EOL;
    }
}
