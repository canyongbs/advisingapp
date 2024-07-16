<?php

namespace AdvisingApp\Engagement\Filament\Resources\Actions;

use Closure;
use Exception;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use App\Settings\LicenseSettings;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiSettings;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions\Action;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;

class DraftTemplateWithAiAction extends Action
{
    protected array | Closure $mergeTags = [];

    protected EngagementDeliveryMethod | Closure $deliveryMethod;

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
                $service = app(AiSettings::class)->default_model->getService();

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;

                $mergeTagsList = collect($this->getMergeTags())
                    ->map(fn (string $tag): string => <<<HTML
                        <span data-type="mergeTag" data-id="{$tag}" contenteditable="false">{$tag}</span>
                    HTML)
                    ->join(', ', ' and ');

                if ($this->getDeliveryMethod() === EngagementDeliveryMethod::Sms) {
                    $content = $service->complete(<<<EOL
                        The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                        Please draft a short SMS message template for a student at their college.
                        The user will send a message to you containing instructions for the content.

                        You should only respond with the SMS content, you should never greet them.

                        You may use merge tags to insert dynamic data about the student in the body of the SMS:
                        {$mergeTagsList}
                    EOL, $data['instructions']);

                    $set('content', Str::markdown($content));

                    return;
                }

                $content = $service->complete(<<<EOL
                    The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                    Please draft an email template for a student at their college.
                    The user will send a message to you containing instructions for the content.

                    You should only respond with the email content, you should never greet them.

                    When you answer, it is crucial that you format the email body using rich text in Markdown format.
                    Do not ever mention in your response that the answer is being formatted/rendered in Markdown.

                    You may use merge tags to insert dynamic data about the student in the body of the email:
                    {$mergeTagsList}
                EOL, $data['instructions']);

                $set('content', (string) str($content)->after("\n")->markdown());
            })
            ->visible(
                auth()->user()->hasLicense(LicenseType::ConversationalAi) &&
                app(AiSettings::class)->default_model
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

    public function deliveryMethod(EngagementDeliveryMethod | Closure $method): static
    {
        $this->deliveryMethod = $method;

        return $this;
    }

    public function getDeliveryMethod(): EngagementDeliveryMethod
    {
        return $this->evaluate($this->deliveryMethod ?? throw new Exception('The [deliveryMethod()] must be set when using [' . static::class . '].'));
    }
}
