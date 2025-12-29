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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\Submission;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;

class CreateProspectFromSubmission
{
    public function __invoke(Submission $submission): ?Prospect
    {
        $submissible = $submission->submissible;

        if (! $submissible instanceof Form || ! $submissible->generate_prospects) {
            return null;
        }

        if ($submission->author) {
            return null;
        }

        $email = $this->extractEmailFromSubmission($submission, $submissible);
        $nameData = $this->extractNameFromSubmission($submission, $submissible);

        if (! $email || ! $nameData) {
            return null;
        }

        $prospect = Prospect::query()->make([
            'first_name' => $nameData['first_name'],
            'last_name' => $nameData['last_name'],
            'preferred' => $nameData['preferred'] ?? null,
            'full_name' => "{$nameData['first_name']} {$nameData['last_name']}",
        ]);

        $status = ProspectStatus::query()
            ->where('classification', SystemProspectClassification::New)
            ->first();

        if ($status) {
            $prospect->status()->associate($status);
        }

        $source = ProspectSource::query()
            ->where('name', 'Advising App')
            ->first();

        if ($source) {
            $prospect->source()->associate($source);
        }

        $prospect->save();

        $emailAddress = $prospect->emailAddresses()->create([
            'address' => $email,
            'order' => 1,
        ]);

        $prospect->primaryEmailAddress()->associate($emailAddress);
        $prospect->save();

        $submission->author()->associate($prospect);
        $submission->save();

        return $prospect;
    }

    protected function extractEmailFromSubmission(Submission $submission, Form $form): ?string
    {
        $emailField = $form->fields()
            ->where('type', EducatableEmailFormFieldBlock::type())
            ->first();

        if (! $emailField) {
            return null;
        }

        $fieldSubmission = $submission->fields()
            ->where('form_fields.id', $emailField->id)
            ->first();

        if (! $fieldSubmission) {
            return null;
        }

        return $fieldSubmission->pivot->response ?? null;
    }

    /**
     * @return array{first_name: string, last_name: string, preferred: string|null}|null
     */
    protected function extractNameFromSubmission(Submission $submission, Form $form): ?array
    {
        $nameField = $form->fields()
            ->where('type', EducatableNameFormFieldBlock::type())
            ->first();

        if (! $nameField) {
            return null;
        }

        $fieldSubmission = $submission->fields()
            ->where('form_fields.id', $nameField->id)
            ->first();

        if (! $fieldSubmission) {
            return null;
        }

        $nameData = $fieldSubmission->pivot->response ?? null;

        if (is_string($nameData)) {
            $nameData = json_decode($nameData, true);
        }

        if (! is_array($nameData)) {
            return null;
        }

        $firstName = $nameData['first_name'] ?? null;
        $lastName = $nameData['last_name'] ?? null;

        if (! $firstName || ! $lastName) {
            return null;
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'preferred' => $nameData['preferred'] ?? null,
        ];
    }
}
