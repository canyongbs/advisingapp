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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

namespace AdvisingApp\Ai\Filament\Pages;

use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Enums\NavigationGroup;
use App\Features\SmartPromptInstructionsFeature;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSmartPromptInstructionsSettings extends SettingsPage
{
    protected static string $settings = AiSettings::class;

    protected static ?string $title = 'Smart Prompt Instructions';

    protected static ?string $navigationLabel = 'Smart Prompt Instructions';

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::GlobalAdministration;

    protected static ?int $navigationSort = 15;

    public static function canAccess(): bool
    {
        // TODO: Cleanup Task - Remove the SmartPromptInstructionsFeature check once the flag is cleaned up.
        if (! SmartPromptInstructionsFeature::active()) {
            return false;
        }

        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->canAccessAiSettings();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Instructions')
                    ->description('These instructions are prepended to every smart prompt before it is sent to the AI Advisor. Use the merge tags below to insert details about the selected prompt.')
                    ->schema([
                        RichEditor::make('smart_prompt_instructions')
                            ->hiddenLabel()
                            ->required()
                            ->json()
                            ->toolbarButtons([
                                ['bold', 'italic'],
                                ['bulletList', 'orderedList'],
                                ['mergeTags'],
                            ])
                            ->activePanel('mergeTags')
                            ->mergeTags([
                                'prompt title',
                                'prompt category',
                                'prompt description',
                            ])
                            ->helperText('Insert a “{{” to see the list of available merge tags. The content of the selected prompt is automatically appended after these instructions.')
                            ->hintAction(
                                Action::make('resetSmartPromptInstructions')
                                    ->label('Reset to default')
                                    ->icon('heroicon-m-arrow-path')
                                    ->requiresConfirmation()
                                    ->modalHeading('Reset instructions to default?')
                                    ->modalDescription('This will replace the current instructions with the system default. You can save or discard the change afterwards.')
                                    ->action(fn (Set $set) => $set('smart_prompt_instructions', AiSettings::defaultSmartPromptInstructions()))
                            )
                            ->columnSpanFull(),
                    ]),
            ])
            ->disabled(! auth()->user()->canAccessAiSettings());
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (blank($data['smart_prompt_instructions'] ?? null)) {
            $data['smart_prompt_instructions'] = AiSettings::defaultSmartPromptInstructions();
        }

        return $data;
    }

    public function save(): void
    {
        if (! auth()->user()->canAccessAiSettings()) {
            return;
        }

        parent::save();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->canAccessAiSettings()) {
            return [];
        }

        return parent::getFormActions();
    }
}
