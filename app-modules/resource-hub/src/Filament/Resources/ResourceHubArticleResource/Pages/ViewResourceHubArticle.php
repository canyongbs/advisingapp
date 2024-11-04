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

namespace AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\RateLimiter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use AdvisingApp\ResourceHub\Models\KnowledgeBaseArticle;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticleResource;

class ViewResourceHubArticle extends ViewRecord
{
    protected static string $resource = ResourceHubArticleResource::class;

    public function getTitle(): string | Htmlable
    {
        return $this->record->title;
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $resourceHubArticle = $this->getRecord();

        RateLimiter::attempt(
            "view-knowledge-base-article-{$resourceHubArticle->getKey()}-user-" . auth()->id(),
            1,
            fn () => $resourceHubArticle->views()->create(['user_id' => auth()->id()]),
        );
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Article Information')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Article Title')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                        TextEntry::make('public')
                            ->label('Public')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('views_count')
                            ->label('Views')
                            ->state(fn (KnowledgeBaseArticle $record): int => $record->views()->count()),
                    ])
                    ->columns(2),
                Section::make()
                    ->schema([
                        ViewEntry::make('article_details')
                            ->label('Article Details')
                            ->columnSpanFull()
                            ->view('filament.infolists.components.html'),
                    ]),
                Section::make('Article Metadata')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('quality.name')
                            ->label('Quality'),
                        TextEntry::make('category.name')
                            ->label('Category'),
                        TextEntry::make('division.name')
                            ->label('Division'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $resourceHubArticle = $this->getRecord();

        return [
            Action::make('upvote')
                ->label(fn (): string => ($resourceHubArticle->isUpvoted() ? 'Upvoted ' : 'Upvote ') . "({$resourceHubArticle->upvotes()->count()})")
                ->color(fn (): string => $resourceHubArticle->isUpvoted() ? 'success' : 'gray')
                ->icon(fn (): ?string => $resourceHubArticle->isUpvoted() ? 'heroicon-m-chevron-up' : null)
                ->action(fn () => $resourceHubArticle->toggleUpvote()),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
