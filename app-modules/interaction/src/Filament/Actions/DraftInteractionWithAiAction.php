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

namespace AdvisingApp\Interaction\Filament\Actions;

use AdvisingApp\Ai\Actions\CompletePrompt;
use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;

class DraftInteractionWithAiAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn (Page | RelationManager $livewire) => view('interaction::filament.actions.draft-with-ai-modal-content', [
                'recordTitle' => ($livewire instanceof RelationManager ? $livewire->getOwnerRecord() : $livewire->getRecord())->full_name,
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
            ->action(function (array $data, Get $get, Set $set, Page | RelationManager $livewire) {
                $aiModel = app(AiIntegratedAssistantSettings::class)->default_model;

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;

                $context = collect();

                if (! is_null($get('interaction_initiative_id')) && $initiative = InteractionInitiative::find($get('interaction_initiative_id'))) {
                    $context->push("- Related Initiative: Explain this as the specific project or goal that the interaction is part of. Mention the related initiative {$initiative->name}.");
                }

                if (! is_null($get('interaction_driver_id')) && $driver = InteractionDriver::find($get('interaction_driver_id'))) {
                    $context->push("- Call Driver: Describe {$driver->name} as the reason or motivation for making the call or engaging in the interaction.");
                }

                if (! is_null($get('interaction_outcome_id')) && $outcome = InteractionOutcome::find($get('interaction_outcome_id'))) {
                    $context->push("- Interaction Outcome: Describe {$outcome->name} as the result or effect of the interaction.");
                }

                if (! is_null($get('interaction_type_id')) && $type = InteractionType::find($get('interaction_type_id'))) {
                    $context->push("- Type of Engagement: Explain {$type->name} as the nature of the interaction, such as whether it was a meeting, call, or another form of communication.");
                }

                $additionalContext = $context->isNotEmpty() ? $context->implode("\n") : '';

                $record = ($livewire instanceof RelationManager ? $livewire->getOwnerRecord() : $livewire->getRecord());
                $modelName = match ($record::class) {
                    Prospect::class => 'prospect',
                    default => 'student',
                };
                $recordFullName = $record->full_name;

                try {
                    $content = app(CompletePrompt::class)->execute(
                        aiModel: $aiModel,
                        prompt: <<<EOL
                            My name is {$userName}, and I am a {$userJobTitle} at {$clientName}.

                            Please document my interaction with the {$modelName} {$recordFullName} at our college based on the following details:

                            Instructions:
                            - Respond only with the interaction content—no greetings or additional comments.
                            - The first line should be the raw subject of the interaction with no "Subject: " label, written in plain text.
                            - The interaction body should start on the second line, using plain text only, with no special formatting.
                            - Never mention in your response that the content is formatted or rendered in plain text.
                            - Use the following context, only if it's available and not blank , to enhance the interaction body:
                            {$additionalContext}
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
