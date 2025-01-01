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

namespace AdvisingApp\Engagement\Filament\Resources\Actions;

use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Notification\Enums\NotificationChannel;
use App\Settings\LicenseSettings;
use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

class DraftTemplateWithAiAction extends Action
{
    protected array | Closure $mergeTags = [];

    protected NotificationChannel | Closure $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn () => view('engagement::filament.resources.draft-template-with-ai-modal-content', [
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
            ->action(function (array $data, Get $get, Set $set) {
                $model = app(AiIntegratedAssistantSettings::class)->default_model;

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;

                $mergeTagsList = collect($this->getMergeTags())
                    ->map(fn (string $tag): string => <<<HTML
                        <span data-type="mergeTag" data-id="{$tag}" contenteditable="false">{$tag}</span>
                    HTML)
                    ->join(', ', ' and ');

                if ($this->getDeliveryMethod() === NotificationChannel::Sms) {
                    try {
                        $content = app(CompletePrompt::class)->execute(
                            aiModel: $model,
                            prompt: <<<EOL
                                The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                                Please draft a short SMS message template for a student at their college.
                                The user will send a message to you containing instructions for the content.

                                You should only respond with the SMS content, you should never greet them.

                                You may use merge tags to insert dynamic data about the student in the body of the SMS:
                                {$mergeTagsList}
                            EOL,
                            content: $data['instructions'],
                        );
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

                    $set('content', Str::markdown($content));

                    return;
                }

                try {
                    $content = app(CompletePrompt::class)->execute(
                        aiModel: $model,
                        prompt: <<<EOL
                            The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                            Please draft an email template for a student at their college.
                            The user will send a message to you containing instructions for the content.

                            You should only respond with the email content, you should never greet them.

                            When you answer, it is crucial that you format the email body using rich text in Markdown format.
                            Do not ever mention in your response that the answer is being formatted/rendered in Markdown.

                            You may use merge tags to insert dynamic data about the student in the body of the email:
                            {$mergeTagsList}
                        EOL,
                        content: $data['instructions'],
                    );
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

                $set('content', (string) str($content)->after("\n")->markdown());
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

    public function channel(NotificationChannel | Closure $method): static
    {
        $this->channel = $method;

        return $this;
    }

    public function getDeliveryMethod(): NotificationChannel
    {
        return $this->evaluate($this->channel ?? throw new Exception('The [channel()] must be set when using [' . static::class . '].'));
    }
}
