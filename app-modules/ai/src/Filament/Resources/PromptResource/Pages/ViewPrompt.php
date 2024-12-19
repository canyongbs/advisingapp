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

namespace AdvisingApp\Ai\Filament\Resources\PromptResource\Pages;

use AdvisingApp\Ai\Filament\Resources\PromptResource;
use AdvisingApp\Ai\Filament\Resources\PromptTypeResource;
use AdvisingApp\Ai\Models\Prompt;
use App\Features\SmartPromptsFeature;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPrompt extends ViewRecord
{
    protected static string $resource = PromptResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('type.title')
                            ->url(fn (Prompt $record) => PromptTypeResource::getUrl('view', ['record' => $record->type])),
                        TextEntry::make('uses')
                            ->state(fn (Prompt $record): int => $record->uses()->count()),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('prompt')
                            ->columnSpanFull()
                            ->visible(fn (Prompt $record): bool => ! $record->is_smart || auth()->user()->isSuperAdmin()),
                        TextEntry::make('is_smart')
                            ->label('Kind')
                            ->state(fn (Prompt $record): string => $record->is_smart ? 'Smart' : 'Custom')
                            ->visible(SmartPromptsFeature::active()),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $prompt = $this->getRecord();

        return [
            Action::make('upvote')
                ->label(fn (): string => ($prompt->isUpvoted() ? 'Upvoted ' : 'Upvote ') . "({$prompt->upvotes()->count()})")
                ->color(fn (): string => $prompt->isUpvoted() ? 'success' : 'gray')
                ->icon(fn (): ?string => $prompt->isUpvoted() ? 'heroicon-m-chevron-up' : null)
                ->action(fn () => $prompt->toggleUpvote()),
            EditAction::make(),
        ];
    }
}
