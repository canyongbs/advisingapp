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

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormFieldSubmission;
use AdvisingApp\Form\Models\Submission;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessSubmissionField
{
    public function __construct(
        protected ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail
    ) {}

    public function __invoke(Submission $submission, string $fieldId, mixed $response, array $fields): void
    {
        $submission->fields()->attach($fieldId, [
            'id' => Str::orderedUuid(),
            'response' => $response,
        ]);

        /** @var FormField|null $field */
        $field = $submission->fields()->find($fieldId);

        if ($field && $field->type === UploadFormFieldBlock::type() && is_array($response)) {
            /** @var FormFieldSubmission $formFieldSubmission */
            $formFieldSubmission = $field->pivot;

            foreach ($response as $file) {
                $key = ltrim($file['path'], '/');

                $media = $formFieldSubmission
                    ->addMediaFromDisk($key, 's3')
                    ->usingFileName($file['originalFileName'] ?? basename($key))
                    ->toMediaCollection('files', 's3');

                Storage::disk('s3')->delete($key);
                $formFieldSubmission->update([
                    'response' => [
                        'media_id' => $media->id,
                        'file_name' => $media->file_name,
                        'original_name' => $file['originalFileName'] ?? $media->file_name,
                    ],
                ]);
            }
        }

        if ($fields[$fieldId] === EducatableEmailFormFieldBlock::type()) {
            $this->handleEducatableEmailField($submission, $response);
        }
    }

    protected function handleEducatableEmailField(Submission $submission, mixed $response): void
    {
        $submissible = $submission->submissible;

        if ($submission->author && $submission->author instanceof Prospect) {
            if (in_array($submissible::class, [Form::class, Application::class])) {
                $this->updateProspectEmail($submission->author, $response);
            }

            return;
        }

        if ($submission->author) {
            return;
        }

        $author = ($this->resolveSubmissionAuthorFromEmail)($response);

        if ($author) {
            $submission->author()->associate($author);
        }
    }

    protected function updateProspectEmail(Prospect $prospect, ?string $newEmail): void
    {
        if (blank($newEmail)) {
            return;
        }

        $currentEmail = $prospect->primaryEmailAddress?->address;

        if ($currentEmail === $newEmail) {
            return;
        }

        if ($prospect->primaryEmailAddress) {
            $prospect->primaryEmailAddress->update([
                'address' => $newEmail,
            ]);
        } else {
            $emailAddress = $prospect->emailAddresses()->create([
                'address' => $newEmail,
                'order' => 1,
            ]);

            $prospect->primaryEmailAddress()->associate($emailAddress);
            $prospect->save();
        }
    }
}
