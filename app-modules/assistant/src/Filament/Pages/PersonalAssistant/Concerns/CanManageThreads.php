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

namespace AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Actions\Action;
use Laravel\Pennant\Feature;
use AdvisingApp\Team\Models\Team;
use Filament\Actions\StaticAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\ActionSize;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Assistant\Enums\AssistantChatShareVia;
use AdvisingApp\Assistant\Jobs\ShareAssistantChatsJob;
use AdvisingApp\Assistant\Enums\AssistantChatShareWith;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

trait CanManageThreads
{
    public function saveThreadAction(): Action
    {
        return Action::make('saveChat')
            ->label('Save')
            ->modalHeading('Save chat')
            ->modalSubmitActionLabel('Save')
            ->icon('heroicon-s-bookmark')
            ->link()
            ->size(ActionSize::Small)
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Name this chat')
                    ->required(),
                $this->folderSelect(),
            ])
            ->modalWidth('md')
            ->action(function (array $data) {
                if (filled($this->thread->id)) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                /** @var AssistantChat $assistantChat */
                $assistantChat = $user->assistantChats()->create([
                    'name' => $data['name'],
                    ...((Feature::active('custom-ai-assistants') && $this->aiAssistant) ? [
                        'ai_assistant_id' => $this->aiAssistant->getKey(),
                    ] : []),
                    'thread_id' => $this->thread->threadId,
                ]);

                $this->thread->messages->each(function (ChatMessage $message) use ($assistantChat) {
                    $record = $assistantChat->messages()->make($message->toArray());
                    $record->updated_at = $record->created_at;
                    $record->save(['timestamps' => false]);
                });

                $this->thread->id = $assistantChat->id;

                $folder = auth()->user()->assistantChatFolders()->find($data['folder']);

                if (! $folder) {
                    return;
                }

                $this->moveChat($assistantChat, $folder);
            });
    }

    public function editThreadAction(): Action
    {
        return Action::make('editChat')
            ->modalSubmitActionLabel('Save')
            ->modalWidth('md')
            ->size(ActionSize::ExtraSmall)
            ->form([
                TextInput::make('name')
                    ->label('Name')
                    ->autocomplete(false)
                    ->placeholder('Rename this chat')
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                $chat = auth()->user()->assistantChats()->find($arguments['chat']);

                if (! $chat) {
                    return;
                }

                $chat->update($data);

                $this->threads = $this->threads->map(function (AssistantChat $chat) use ($arguments, $data) {
                    if ($chat->id === $arguments['chat']) {
                        $chat->name = $data['name'];
                    }

                    return $chat;
                });
            })
            ->icon('heroicon-o-pencil')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'))
            ->iconButton()
            ->extraAttributes([
                'class' => 'relative inline-flex w-5 h-5 hidden group-hover:inline-flex',
            ]);
    }

    public function cloneThreadAction(): Action
    {
        return Action::make('cloneChat')
            ->label('Clone')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('target_type')
                    ->label('With')
                    ->options(AssistantChatShareWith::class)
                    ->enum(AssistantChatShareWith::class)
                    ->default(AssistantChatShareWith::default())
                    ->required()
                    ->live(),
                Select::make('target_ids')
                    ->label(fn (Get $get): string => match ($get('target_type')) {
                        AssistantChatShareWith::Team => 'Select Teams',
                        AssistantChatShareWith::User => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('target_type')))
                    ->options(function (Get $get): Collection {
                        return match ($get('target_type')) {
                            AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                            AssistantChatShareWith::User => User::whereKeyNot(auth()->id())->orderBy('name')->pluck('name', 'id'),
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                /** @var User $sender */
                $sender = auth()->user();

                $chat = auth()->user()->assistantChats()->find($arguments['thread']);

                if (! $chat) {
                    return;
                }

                dispatch(new ShareAssistantChatsJob($chat, AssistantChatShareVia::Internal, $data['target_type'], $data['target_ids'], $sender));
            })
            ->link()
            ->icon('heroicon-m-document-duplicate')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }

    public function emailThreadAction(): Action
    {
        return Action::make('emailChat')
            ->label('Email')
            ->modalSubmitActionLabel('Continue')
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth('md')
            ->form([
                Radio::make('target_type')
                    ->label('With')
                    ->options(AssistantChatShareWith::class)
                    ->enum(AssistantChatShareWith::class)
                    ->default(AssistantChatShareWith::default())
                    ->required()
                    ->live(),
                Select::make('target_ids')
                    ->label(fn (Get $get): string => match ($get('target_type')) {
                        AssistantChatShareWith::Team => 'Select Teams',
                        AssistantChatShareWith::User => 'Select Users',
                    })
                    ->visible(fn (Get $get): bool => filled($get('target_type')))
                    ->options(function (Get $get): Collection {
                        return match ($get('target_type')) {
                            AssistantChatShareWith::Team => Team::orderBy('name')->pluck('name', 'id'),
                            AssistantChatShareWith::User => User::orderBy('name')->pluck('name', 'id'),
                        };
                    })
                    ->searchable()
                    ->multiple()
                    ->required(),
            ])
            ->action(function (array $arguments, array $data) {
                /** @var User $sender */
                $sender = auth()->user();

                $chat = auth()->user()->assistantChats()->find($arguments['thread']);

                if (! $chat) {
                    return;
                }

                dispatch(new ShareAssistantChatsJob($chat, AssistantChatShareVia::Email, $data['target_type'], $data['target_ids'], $sender));
            })
            ->link()
            ->icon('heroicon-m-envelope')
            ->color('warning')
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('primary'));
    }
}
