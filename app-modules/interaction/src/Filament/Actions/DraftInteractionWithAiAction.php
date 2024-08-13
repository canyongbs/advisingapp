<?php

namespace AdvisingApp\Interaction\Filament\Actions;

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

class DraftInteractionWithAiAction extends Action
{
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
                $model = $livewire::getResource()::getModelLabel();

                $context = collect();

                if ($id = $get('interaction_initiative_id')) {
                    if ($initiative = InteractionInitiative::find($id)) {
                        $context->push("- Mention the related initiative: {$initiative->name}.");
                    }
                }

                if ($id = $get('interaction_driver_id')) {
                    if ($driver = InteractionDriver::find($id)) {
                        $context->push("- Include the call driver: {$driver->name}.");
                    }
                }

                if ($id = $get('interaction_outcome_id')) {
                    if ($outcome = InteractionOutcome::find($id)) {
                        $context->push("- State the interaction outcome: {$outcome->name}.");
                    }
                }

                if ($id = $get('interaction_type_id')) {
                    if ($type = InteractionType::find($id)) {
                        $context->push("- Specify the type of engagement: {$type->name}.");
                    }
                }

                $additionalContext = $context->isNotEmpty() ? $context->implode("\n") : '';

                try {
                    $content = $service->complete(
                        <<<EOL
                            My name is {$userName}, and I am a {$userJobTitle} at {$clientName}.

                            Please document my interaction with the {$model} {$livewire->record}['full_name'] at our college based on the following details:

                            Instructions:
                            - Respond only with the interaction content—no greetings or additional comments.
                            - The first line should be the raw subject of the interaction with no "Subject: " label, written in plain text.
                            - The interaction body should start on the second line, using plain text only, with no special formatting.
                            - Never mention in your response that the content is formatted or rendered in plain text.
                            - Use the following context, only if it's available and not blank , to enhance the interaction body (but not the subject line):
                            {$additionalContext}
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
}
