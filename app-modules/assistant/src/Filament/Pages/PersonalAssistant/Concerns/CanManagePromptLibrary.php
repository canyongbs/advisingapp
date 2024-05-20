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

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use AdvisingApp\Assistant\Models\Prompt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use AdvisingApp\Assistant\Models\PromptType;
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
                $prompt->id => view('assistant::filament.pages.personal-assistant.prompt-option', ['prompt' => $prompt])->render(),
            ])
            ->all();

        return Action::make('insertFromPromptLibrary')
            ->label('Prompt library')
            ->color('gray')
            ->form([
                Select::make('typeId')
                    ->label('Filter by type')
                    ->hint('Optional')
                    ->options(fn (): array => PromptType::query()
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all())
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => (Prompt::find($get('promptId'))?->type_id !== $state) ?
                        $set('promptId', null) :
                        null)
                    ->live(),
                Checkbox::make('myPrompts')
                    ->label('My prompts only')
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => ($state && ! Prompt::find($get('promptId'))?->user->is(auth()->user())) ?
                        $set('promptId', null) :
                        null)
                    ->live(),
                Select::make('promptId')
                    ->label('Select a prompt')
                    ->searchable()
                    ->allowHtml()
                    ->options(fn (Get $get): array => $getPromptOptions(Prompt::query()
                        ->limit(50)
                        ->when(
                            filled($get('typeId')),
                            fn (Builder $query) => $query->where('type_id', $get('typeId')),
                        )
                        ->when(
                            $get('myPrompts'),
                            fn (Builder $query) => $query->whereBelongsTo(auth()->user()),
                        )))
                    ->getSearchResultsUsing(function (Get $get, string $search) use ($getPromptOptions): array {
                        $search = (string) str($search)->wrap('%');

                        return $getPromptOptions(Prompt::query()
                            ->limit(50)
                            ->where(fn (Builder $query) => $query
                                ->where(new Expression('lower(title)'), 'like', $search)
                                ->orWhere(new Expression('lower(description)'), 'like', $search)
                                ->orWhere(new Expression('lower(prompt)'), 'like', $search))
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
                    ->required(),
            ])
            ->modalWidth(MaxWidth::ExtraLarge)
            ->action(function (array $data) {
                $prompt = Prompt::find($data['promptId']);

                if (! $prompt) {
                    return;
                }

                $this->message = $prompt->prompt;

                $prompt->uses()->create([
                    'user_id' => auth()->id(),
                ]);
            });
    }
}
