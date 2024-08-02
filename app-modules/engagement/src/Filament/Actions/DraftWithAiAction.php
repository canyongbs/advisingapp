<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Filament\Actions;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use App\Settings\LicenseSettings;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiSettings;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;

class DraftWithAiAction extends Action
{
    protected array | Closure $mergeTags = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn (Page $livewire) => view('engagement::filament.actions.draft-with-ai-modal-content', [
                'recordTitle' => $livewire::getResource()::getPluralModelLabel(),
                'avatarUrl' => AiAssistant::query()->where('is_default', true)->first()
                    ?->getFirstTemporaryUrl(now()->addHour(), 'avatar', 'avatar-height-250px') ?: Vite::asset('resources/images/canyon-ai-headshot.jpg'),
            ]))
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitActionLabel('Draft')
            ->form([
                Textarea::make('instructions')
                    ->hiddenLabel()
                    ->rows(4)
                    ->placeholder('What do you want to write about?')
                    ->required(),
            ])
            ->action(function (array $data, Get $get, Set $set, Page $livewire) {
                $service = Feature::active('ai-integrated-assistant-settings')
                    ? app(AiIntegratedAssistantSettings::class)->default_model->getService()
                    : app(AiSettings::class)->default_model->getService();

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;
                $educatableLabel = $livewire::getResource()::getPluralModelLabel();

                $mergeTagsList = collect($this->getMergeTags())
                    ->map(fn (string $tag): string => <<<HTML
                        <span data-type="mergeTag" data-id="{$tag}" contenteditable="false">{$tag}</span>
                    HTML)
                    ->join(', ', ' and ');

                if ($get('delivery_method') === EngagementDeliveryMethod::Sms->value) {
                    try {
                        $content = $service->complete(<<<EOL
                            The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                            Please draft a short SMS message for {$educatableLabel} at their college.
                            The user will send a message to you containing instructions for the content.

                            You should only respond with the SMS content, you should never greet them.

                            You may use merge tags to insert dynamic data about the student in the body of the SMS:
                            {$mergeTagsList}
                        EOL, $data['instructions']);
                    } catch (MessageResponseException $exception) {
                        report($exception);

                        Notification::make()
                            ->title('AI Assistant Error')
                            ->body('There was an issue using the AI assistant. Please try again later.')
                            ->danger()
                            ->send();

                        $this->halt();

                        return;
                    }

                    $set('body', Str::markdown($content));

                    return;
                }

                try {
                    $content = $service->complete(<<<EOL
                        The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                        Please draft an email for {$educatableLabel} at their college.
                        The user will send a message to you containing instructions for the content.

                        You should only respond with the email content, you should never greet them.
                        The first line should contain the raw subject of the email, with no "Subject: " label at the start.
                        All following lines after the subject are the email body.

                        When you answer, it is crucial that you format the email body using rich text in Markdown format.
                        The subject line can not use Markdown formatting, it is plain text.
                        Do not ever mention in your response that the answer is being formatted/rendered in Markdown.

                        You may use merge tags to insert dynamic data about the student in the body of the email, but these do not work in the subject line:
                        {$mergeTagsList}
                    EOL, $data['instructions']);
                } catch (MessageResponseException $exception) {
                    report($exception);

                    Notification::make()
                        ->title('AI Assistant Error')
                        ->body('There was an issue using the AI assistant. Please try again later.')
                        ->danger()
                        ->send();

                    $this->halt();

                    return;
                }

                $set('subject', (string) str($content)
                    ->before("\n")
                    ->trim());

                $set('body', (string) str($content)->after("\n")->markdown());
            })
            ->visible(
                auth()->user()->hasLicense(LicenseType::ConversationalAi)
            );
    }

    public static function getDefaultName(): ?string
    {
        return 'draftWithAi';
    }

    public function mergeTags(array | Closure $tags): static
    {
        $this->mergeTags = $tags;

        return $this;
    }

    public function getMergeTags(): array
    {
        return $this->evaluate($this->mergeTags);
    }
}
