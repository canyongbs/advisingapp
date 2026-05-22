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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Application\Notifications;

use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationField;
use AdvisingApp\Application\Models\ApplicationFieldSubmission;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class ApplicationSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param Application $application
     * @param ApplicationSubmission $applicationSubmission
     * @param array<int, string> $channels
     */
    public function __construct(
        public Application $application,
        public ApplicationSubmission $applicationSubmission,
        public array $channels = []
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(User $notifiable): MailMessage
    {
        $data = $this->buildViewData($notifiable);

        return (new MailMessage())
            ->subject("New Application Submission: {$data['applicationName']}")
            ->markdown('application::mail.application-submission', $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        $data = $this->buildViewData($notifiable);
        $authorName = $data['hasAuthorRecord']
            ? "<a href='{$data['authorUrl']}'><u><b>{$data['firstName']} {$data['lastName']}</b></u></a>"
            : "<b>{$data['firstName']} {$data['lastName']}</b>";

        return FilamentNotification::make()
            ->info()
            ->title('New Application Submission')
            ->body("{$authorName} ({$data['email']}) submitted the <a href='{$data['applicationUrl']}'><u><b>{$data['applicationName']}</b></u></a> application on {$data['timestamp']}. <a href='{$data['submissionUrl']}'><u><b>View Submission</b></u></a>.")
            ->getDatabaseMessage();
    }

    /**
     * @return array{firstName: string, lastName: string, email: string, authorUrl: string, hasAuthorRecord: bool, applicationName: string, applicationUrl: string, submissionUrl: string, timestamp: string, formData: array<string, string>}
     */
    protected function buildViewData(User $notifiable): array
    {
        $this->applicationSubmission->loadMissing(['author', 'fields']);

        $submissionUrl = ApplicationResource::getUrl('manage-submissions', [
            'record' => $this->application,
            'tableAction' => 'view',
            'tableActionRecord' => $this->applicationSubmission->id,
        ]);

        $applicationUrl = ApplicationResource::getUrl('edit', ['record' => $this->application]);

        $timestamp = now()->tz($notifiable->timezone ?? 'UTC')->format('M j, Y g:i a (T)');

        $author = $this->applicationSubmission->author;
        $firstName = 'Unknown';
        $lastName = 'Unknown';
        $email = 'No email';

        $authorUrl = '#';
        $hasAuthorRecord = false;

        if ($author instanceof Student) {
            $authorUrl = StudentResource::getUrl('view', ['record' => $author]);
            $hasAuthorRecord = true;
            $firstName = $author->first ?? 'Unknown';
            $lastName = $author->last ?? 'Unknown';
            $email = $author->primaryEmailAddress->address ?? 'No email';
        } elseif ($author instanceof Prospect) {
            $authorUrl = ProspectResource::getUrl('view', ['record' => $author]);
            $hasAuthorRecord = true;
            $firstName = $author->first_name ?? 'Unknown';
            $lastName = $author->last_name ?? 'Unknown';
            $email = $author->primaryEmailAddress->address ?? 'No email';
        }

        $applicationName = (string) ($this->application->name ?? $this->application->title);

        $formData = [];

        /** @var Collection<int, ApplicationField> $fields */
        $fields = $this->applicationSubmission->fields;

        foreach ($fields as $field) {
            /** @var ApplicationFieldSubmission $pivot */
            $pivot = $field->pivot;
            $response = $pivot->response;
            $formData[$field->label] = implode(', ', $response);
        }

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'authorUrl' => $authorUrl,
            'hasAuthorRecord' => $hasAuthorRecord,
            'applicationName' => $applicationName,
            'applicationUrl' => $applicationUrl,
            'submissionUrl' => $submissionUrl,
            'timestamp' => $timestamp,
            'formData' => $formData,
        ];
    }
}
