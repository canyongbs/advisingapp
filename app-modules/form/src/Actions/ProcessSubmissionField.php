<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Application\Models\ApplicationFieldSubmission;
use AdvisingApp\Engagement\Models\EngagementFile;
use AdvisingApp\Form\Filament\Blocks\EducatableAddressFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableBirthdateFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableNameFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatablePhoneNumberFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\EducatableUploadFormFieldBlock;
use AdvisingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormFieldSubmission;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Form\Models\Submission;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormFieldSubmission;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Settings\ImportSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class ProcessSubmissionField
{
    public function __construct(
        protected ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail
    ) {}

    public function __invoke(Submission $submission, string $fieldId, mixed $response, array $fields): void
    {
        $submission->fields()->attach($fieldId, [
            'id' => Str::orderedUuid(),
            'response' => $response ?? '',
        ]);

        /** @var SubmissibleField|null $field */
        $field = $submission->fields()->find($fieldId);

        if ($field && $field->type === UploadFormFieldBlock::type() && is_array($response)) {
            $fieldSubmission = $field->pivot; /** @phpstan-ignore property.notFound */
            assert($fieldSubmission instanceof FormFieldSubmission || $fieldSubmission instanceof ApplicationFieldSubmission || $fieldSubmission instanceof EventRegistrationFormFieldSubmission);

            foreach ($response as $file) {
                $key = ltrim($file['path'], '/');
                $this->validatePath($key);

                if (! Storage::exists($key)) {
                    continue;
                }

                try{
                    $media = $fieldSubmission
                    ->addMediaFromDisk($key, 's3')
                    ->usingFileName($file['originalFileName'] ?? basename($key))
                    ->toMediaCollection('files', 's3');

                    $fieldSubmission->update([
                        'response' => [
                            'media_id' => $media->id,
                            'file_name' => $media->file_name,
                            'original_name' => $file['originalFileName'] ?? $media->file_name,
                        ],
                    ]);
                } finally {
                    Storage::disk('s3')->delete($key);
                }                
            }
        }

        if ($field && $field->type === EducatableUploadFormFieldBlock::type() && is_array($response)) {
            $this->handleEducatableUploadField($submission, $field, $response);
        }

        if ($fields[$fieldId] === EducatableEmailFormFieldBlock::type()) {
            $this->handleEducatableEmailField($submission, $response);
        }

        if ($fields[$fieldId] === EducatableBirthdateFormFieldBlock::type()) {
            $this->handleEducatableBirthdateField($submission, $response);
        }

        if ($fields[$fieldId] === EducatablePhoneNumberFormFieldBlock::type()) {
            $this->handleEducatablePhoneNumberField($submission, $response);
        }

        if ($fields[$fieldId] === EducatableAddressFormFieldBlock::type()) {
            $this->handleEducatableAddressField($submission, $response);
        }

        if ($fields[$fieldId] === EducatableNameFormFieldBlock::type()) {
            $this->handleEducatableNameField($submission, $response);
        }
    }

    protected function validatePath(string $path): void
    {
        if (str_contains($path, '..') || str_contains($path, '//')) {
            throw new InvalidArgumentException('Invalid path: path traversal not allowed');
        }

        if (! str_starts_with($path, 'tmp/')) {
            throw new InvalidArgumentException('Invalid path: must be within tmp/ directory');
        }

        if (! preg_match('/^tmp\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.[a-zA-Z0-9]+$/i', $path)) {
            throw new InvalidArgumentException('Invalid path: does not match expected format');
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

    protected function handleEducatableBirthdateField(Submission $submission, mixed $response): void
    {
        $submissible = $submission->submissible;

        if (! $submission->author instanceof Prospect) {
            return;
        }

        if (! in_array($submissible::class, [Form::class, Application::class])) {
            return;
        }

        $this->updateProspectBirthdate($submission->author, $response);
    }

    protected function updateProspectBirthdate(Prospect $prospect, mixed $value): void
    {
        if (blank($value)) {
            return;
        }

        $currentValue = $prospect->birthdate;

        if ($currentValue == $value) {
            return;
        }

        $prospect->update([
            'birthdate' => $value,
        ]);
    }

    protected function handleEducatablePhoneNumberField(Submission $submission, mixed $response): void
    {
        $submissible = $submission->submissible;

        if (! $submission->author instanceof Prospect) {
            return;
        }

        if (! in_array($submissible::class, [Form::class, Application::class])) {
            return;
        }

        $this->updateProspectPhoneNumber($submission->author, $response);
    }

    protected function updateProspectPhoneNumber(Prospect $prospect, ?string $newNumber): void
    {
        if (blank($newNumber)) {
            return;
        }

        // Parse and format the phone number to E164 format
        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $formattedNumber = null;

        // Try to parse the number without a region, which will only work if the phone number is in E164 format already.
        try {
            $formattedNumber = $phoneNumberUtil->format(
                $phoneNumberUtil->parse($newNumber),
                PhoneNumberFormat::E164,
            );
        } catch (NumberParseException) {
            // Try with default country
            $defaultCountry = app(ImportSettings::class)->default_country;

            try {
                $formattedNumber = $phoneNumberUtil->format(
                    $phoneNumberUtil->parse($newNumber, $defaultCountry),
                    PhoneNumberFormat::E164,
                );
            } catch (NumberParseException) {
                // If parsing fails, don't update the phone number
                return;
            }
        }

        $currentNumber = $prospect->primaryPhoneNumber?->number;

        if ($currentNumber === $formattedNumber) {
            return;
        }

        if ($prospect->primaryPhoneNumber) {
            $prospect->primaryPhoneNumber->update([
                'number' => $formattedNumber,
            ]);
        } else {
            $phoneNumber = $prospect->phoneNumbers()->create([
                'number' => $formattedNumber,
                'order' => 1,
            ]);

            $prospect->primaryPhoneNumber()->associate($phoneNumber);
            $prospect->save();
        }
    }

    protected function handleEducatableAddressField(Submission $submission, mixed $response): void
    {
        $submissible = $submission->submissible;

        if (! $submission->author instanceof Prospect) {
            return;
        }

        if (! in_array($submissible::class, [Form::class, Application::class])) {
            return;
        }

        $this->updateProspectAddress($submission->author, $response);
    }

    protected function handleEducatableNameField(Submission $submission, mixed $response): void
    {
        $submissible = $submission->submissible;

        if (! $submission->author instanceof Prospect) {
            return;
        }

        if (! in_array($submissible::class, [Form::class, Application::class])) {
            return;
        }

        $this->updateProspectName($submission->author, $response);
    }

    protected function updateProspectName(Prospect $prospect, mixed $nameData): void
    {
        if (! is_array($nameData)) {
            $nameData = json_decode($nameData ?? '{}', true);
        }

        if (blank($nameData)) {
            return;
        }

        $firstName = $nameData['first_name'] ?? null;
        $lastName = $nameData['last_name'] ?? null;
        $preferred = $nameData['preferred'] ?? null;

        $updates = [];

        if (filled($firstName) && $prospect->first_name !== $firstName) {
            $updates['first_name'] = $firstName;
        }

        if (filled($lastName) && $prospect->last_name !== $lastName) {
            $updates['last_name'] = $lastName;
        }

        $preferred = blank($preferred) ? null : $preferred;

        if ($prospect->preferred !== $preferred) {
            $updates['preferred'] = $preferred;
        }

        if (filled($firstName) && filled($lastName)) {
            $fullName = "{$firstName} {$lastName}";

            if ($prospect->full_name !== $fullName) {
                $updates['full_name'] = $fullName;
            }
        }

        if (! empty($updates)) {
            $prospect->update($updates);
        }
    }

    protected function updateProspectAddress(Prospect $prospect, mixed $addressData): void
    {
        if (! is_array($addressData)) {
            $addressData = json_decode($addressData ?? '{}', true);
        }

        if (blank($addressData)) {
            return;
        }

        $hasData = filled($addressData['line_1'] ?? null)
            || filled($addressData['line_2'] ?? null)
            || filled($addressData['line_3'] ?? null)
            || filled($addressData['city'] ?? null)
            || filled($addressData['state'] ?? null)
            || filled($addressData['postal'] ?? null)
            || filled($addressData['country'] ?? null);

        if (! $hasData) {
            return;
        }

        $addressAttributes = [
            'line_1' => $addressData['line_1'] ?? null,
            'line_2' => $addressData['line_2'] ?? null,
            'line_3' => $addressData['line_3'] ?? null,
            'city' => $addressData['city'] ?? null,
            'state' => $addressData['state'] ?? null,
            'postal' => $addressData['postal'] ?? null,
            'country' => $addressData['country'] ?? null,
        ];

        if ($prospect->primaryAddress) {
            $prospect->primaryAddress->update($addressAttributes);
        } else {
            $address = $prospect->addresses()->create([
                ...$addressAttributes,
                'order' => 1,
            ]);

            $prospect->primaryAddress()->associate($address);
            $prospect->save();
        }
    }

    /**
     * @param array<int, array{path: string, originalFileName?: string}> $response
     */
    protected function handleEducatableUploadField(Submission $submission, SubmissibleField $field, array $response): void
    {
        $submissible = $submission->submissible;

        if (! in_array($submissible::class, [Form::class, Application::class, EventRegistrationForm::class])) {
            return;
        }

        $author = $submission->author;

        // @phpstan-ignore-next-line instanceof.alwaysFalse
        if (! ($author instanceof Student || $author instanceof Prospect || $author instanceof EventAttendee)) {
            return;
        }

        $fieldSubmission = $field->pivot; /** @phpstan-ignore property.notFound */
        assert($fieldSubmission instanceof FormFieldSubmission || $fieldSubmission instanceof EventRegistrationFormFieldSubmission);

        foreach ($response as $file) {
            $key = ltrim($file['path'], '/');

            $media = $fieldSubmission
                ->addMediaFromDisk($key, 's3')
                ->usingFileName($file['originalFileName'] ?? basename($key))
                ->toMediaCollection('files', 's3');

            $fieldSubmission->update([
                'response' => [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name,
                    'original_name' => $file['originalFileName'] ?? $media->file_name,
                ],
            ]);

            $engagementFile = EngagementFile::create([
                'description' => "Uploaded via form: {$submissible->name} - {$field->label}",
            ]);

            $engagementFile
                ->addMediaFromDisk($media->getPathRelativeToRoot(), $media->disk)
                ->usingFileName($media->file_name)
                ->preservingOriginal()
                ->toMediaCollection('file', 's3');

            // @phpstan-ignore instanceof.alwaysTrue, booleanOr.alwaysTrue (EventAttendee excluded - doesn't have engagementFiles)
            if ($author instanceof Student || $author instanceof Prospect) {
                $author->engagementFiles()->attach($engagementFile);
            }

            Storage::disk('s3')->delete($key);
        }
    }
}
