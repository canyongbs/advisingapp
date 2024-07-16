<?php

namespace AdvisingApp\Engagement\Filament\ManageRelatedRecords\ManageRelatedEngagementRecords\Actions;

use Closure;
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
use AdvisingApp\Engagement\Filament\ManageRelatedRecords\ManageRelatedEngagementRecords;

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
            ->modalContent(fn (ManageRelatedEngagementRecords $livewire) => view('engagement::filament.manage-related-records.manage-related-engagement-records.draft-with-ai-modal-content', [
                'recordTitle' => $livewire->getRecordTitle(),
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
            ->action(function (array $data, Get $get, Set $set, ManageRelatedEngagementRecords $livewire) {
                $service = app(AiSettings::class)->default_model->getService();

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;
                $educatableLabel = $livewire->getOwnerRecord()::getLabel();

                $mergeTagsList = collect($this->getMergeTags())
                    ->map(fn (string $tag): string => <<<HTML
                        <span data-type="mergeTag" data-id="{$tag}" contenteditable="false">{$tag}</span>
                    HTML)
                    ->join(', ', ' and ');

                if ($get('delivery_method') === EngagementDeliveryMethod::Sms->value) {
                    $content = $service->complete(<<<EOL
                        The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                        Please draft a short SMS message for a {$educatableLabel} at their college.
                        The user will send a message to you containing instructions for the content.

                        You should only respond with the SMS content, you should never greet them.

                        You may use merge tags to insert dynamic data about the student in the body of the SMS:
                        {$mergeTagsList}
                    EOL, $data['instructions']);

                    $set('body', Str::markdown($content));

                    return;
                }

                $content = $service->complete(<<<EOL
                    The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                    Please draft an email for a {$educatableLabel} at their college.
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

                $set('subject', (string) str($content)
                    ->before("\n")
                    ->trim());

                $set('body', (string) str($content)->after("\n")->markdown());
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
}
