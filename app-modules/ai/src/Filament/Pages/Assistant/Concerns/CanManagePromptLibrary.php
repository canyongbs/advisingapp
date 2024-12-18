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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use AdvisingApp\Ai\Models\Prompt;
use Filament\Support\Enums\MaxWidth;
use AdvisingApp\Ai\Models\PromptType;
use App\Features\SmartPromptsFeature;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action as FormComponentAction;

trait CanManagePromptLibrary
{
    public function insertFromPromptLibraryAction(): Action
    {
        $getPromptOptions = fn (Builder $query): array => $query
            ->select(['id', 'title', 'description'])
            ->withCount('upvotes')
            ->withCount('uses')
            ->orderByDesc('upvotes_count')
            ->get()
            ->mapWithKeys(fn (Prompt $prompt) => [
                $prompt->id => view('ai::components.options.prompt', ['prompt' => $prompt])->render(),
            ])
            ->all();

        return Action::make('insertFromPromptLibrary')
            ->label('Prompt library')
            ->color('gray')
            ->form([
                ToggleButtons::make('isSmart')
                    ->label('Would you like to use a pre-built smart prompt or a custom prompt created by your organization?')
                    ->options([
                        1 => 'Smart prompt',
                        0 => 'Custom prompt',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('promptId', null))
                    ->grouped(),
                Select::make('typeId')
                    ->label('Filter by type')
                    ->hint('Optional')
                    ->options(fn (Get $get): array => PromptType::query()
                        ->when(
                            SmartPromptsFeature::active() && $get('isSmart'),
                            fn (Builder $query) => $query->whereRelation('prompts', 'is_smart', true),
                        )
                        ->when(
                            SmartPromptsFeature::active() && ! $get('isSmart'),
                            fn (Builder $query) => $query->whereRelation('prompts', 'is_smart', false),
                        )
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all())
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => (Prompt::find($get('promptId'))?->type_id !== $state) ?
                        $set('promptId', null) :
                        null)
                    ->live()
                    ->hidden(fn (Get $get): bool => blank($get('isSmart'))),
                Checkbox::make('myPrompts')
                    ->label('My prompts only')
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if ($state && ! Prompt::find($get('promptId'))?->user->is(auth()->user())) {
                            $set('promptId', null);
                        }

                        $set('myTeamPrompts', false);
                    })
                    ->live()
                    ->hidden(fn (Get $get): bool => blank($get('isSmart')) || $get('isSmart')),
                Checkbox::make('myTeamPrompts')
                    ->label('My team\'s prompts only')
                    ->afterStateUpdated(function (Set $set) {
                        $set('myPrompts', false);
                        $set('promptId', null);
                    })
                    ->hidden(fn (Get $get): bool => blank($get('isSmart')))
                    ->visible(fn () => count(auth()->user()->teams) ? true : false)
                    ->live(),
                Select::make('promptId')
                    ->label('Select a prompt')
                    ->searchable()
                    ->allowHtml()
                    ->options(
                        fn (Get $get): array => $getPromptOptions(
                            Prompt::query()
                                ->limit(50)
                                ->when(
                                    SmartPromptsFeature::active() && $get('isSmart'),
                                    fn (Builder $query) => $query->where('is_smart', true),
                                )
                                ->when(
                                    SmartPromptsFeature::active() && ! $get('isSmart'),
                                    fn (Builder $query) => $query->where('is_smart', false),
                                )
                                ->when(
                                    filled($get('typeId')),
                                    fn (Builder $query) => $query->where('type_id', $get('typeId')),
                                )
                                ->when(
                                    $get('myPrompts'),
                                    fn (Builder $query) => $query->whereBelongsTo(auth()->user()),
                                )
                                ->when(
                                    $get('myTeamPrompts'),
                                    function (Builder $query) {
                                        /** @var User $user */
                                        $user = auth()->user();
                                        $teamUsers = $user?->teams->first()?->users;

                                        if ($teamUsers) {
                                            $query->whereHas('user', function (Builder $query) use ($teamUsers) {
                                                return $query->whereIn('id', $teamUsers->pluck('id'));
                                            });
                                        }
                                    },
                                )
                        )
                    )
                    ->getSearchResultsUsing(function (Get $get, string $search) use ($getPromptOptions): array {
                        $search = (string) str($search)->wrap('%');

                        return $getPromptOptions(Prompt::query()
                            ->limit(50)
                            ->where(fn (Builder $query) => $query
                                ->where(new Expression('lower(title)'), 'like', $search)
                                ->orWhere(new Expression('lower(description)'), 'like', $search)
                                ->orWhere(new Expression('lower(prompt)'), 'like', $search))
                            ->when(
                                SmartPromptsFeature::active() && $get('isSmart'),
                                fn (Builder $query) => $query->where('is_smart', true),
                            )
                            ->when(
                                SmartPromptsFeature::active() && ! $get('isSmart'),
                                fn (Builder $query) => $query->where('is_smart', false),
                            )
                            ->when(
                                filled($get('typeId')),
                                fn (Builder $query) => $query->where('type_id', $get('typeId')),
                            )
                            ->when(
                                filled($get('myPrompts')),
                                fn (Builder $query) => $query->whereBelongsTo(auth()->user()),
                            ));
                    })
                    ->live()
                    ->suffixAction(function ($state): ?FormComponentAction {
                        if (blank($state)) {
                            return null;
                        }

                        $prompt = Prompt::find($state);

                        if (! $prompt) {
                            return null;
                        }

                        return FormComponentAction::make('upvote')
                            ->label(fn (): string => ($prompt->isUpvoted() ? 'Upvoted ' : 'Upvote ') . "({$prompt->upvotes()->count()})")
                            ->color(fn (): string => $prompt->isUpvoted() ? 'success' : 'gray')
                            ->link()
                            ->icon('heroicon-m-chevron-up')
                            ->action(fn () => $prompt->toggleUpvote());
                    })
                    ->required()
                    ->hidden(fn (Get $get): bool => blank($get('isSmart'))),
            ])
            ->modalWidth(MaxWidth::ExtraLarge)
            ->action(function (array $data) {
                $prompt = Prompt::find($data['promptId']);

                if (! $prompt) {
                    return;
                }

                if ($prompt->is_smart) {
                    $this->dispatch('send-prompt', prompt: ['id' => $prompt->getKey(), 'title' => $prompt->title]);
                } else {
                    $this->dispatch('set-chat-message', content: $prompt->prompt);
                }

                $use = $prompt->uses()->make();
                $use->user()->associate(auth()->user());
                $use->save();
            });
    }
}
