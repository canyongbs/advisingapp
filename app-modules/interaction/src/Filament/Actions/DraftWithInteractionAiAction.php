<?php

namespace AdvisingApp\Interaction\Filament\Actions;

use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;

class DraftWithInteractionAiAction extends Action
{
    protected array | Closure $mergeTags = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn (Page $livewire) => view('interaction::filament.actions.draft-with-ai-modal-content', [
                'recordTitle' => $livewire->record['full_name'],
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
                $modal = $livewire::getResource()::getPluralModelLabel();
                $initiative = InteractionInitiative::find($get('interaction_initiative_id'));
                $driver = InteractionDriver::find($get('interaction_driver_id'));
                $outcome = InteractionOutcome::find($get('interaction_outcome_id'));
                $type = InteractionType::find($get('interaction_type_id'));

                try {
                    $content = $service->complete(
                        <<<EOL
                            My name is {$userName}, and I am a {$userJobTitle} at {$clientName}.

                            Please document my interaction with {$modal} at our college based on the following details:

                            Instructions:
                            - Respond only with the interaction content—no greetings or additional comments.
                            - The first line should be the raw subject of the interaction with no "Subject: " label, written in plain text.
                            - The interaction body should start on the second line, using plain text only, with no special formatting.
                            - Never mention in your response that the content is formatted or rendered in plain text.
                            - Use the following context, if available, to enhance the interaction body (but not the subject line):
                            1. Mention the related initiative: {$initiative?->name}.
                            2. Include the call driver: {$driver?->name}.
                            3. State the interaction outcome: {$outcome?->name}.
                            4. Specify the type of engagement: {$type?->name}.
                        EOL,
                        $data['instructions']
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

                $set('subject', (string) str($content)
                    ->before("\n")
                    ->trim());

                $set('description', (string) str($content)->after("\n")->ltrim("\n"));
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
