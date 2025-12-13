<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\Pages;

use AdvisingApp\BasicNeeds\Filament\Actions\SendEmailAction;
use AdvisingApp\ResourceHub\Filament\Actions\CreateConcernAction;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\ResourceHubArticleResource;
use AdvisingApp\ResourceHub\Filament\Widgets\ResourceHubArticleConcernsTable;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\RateLimiter;

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
            "view-resource-hub-article-{$resourceHubArticle->getKey()}-user-" . auth()->id(),
            1,
            fn () => $resourceHubArticle->views()->create(['user_id' => auth()->id()]),
        );
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tab::make('Content')
                            ->schema([
                                ViewEntry::make('article_details')
                                    ->label('Article Details')
                                    ->columnSpanFull()
                                    ->view('filament.infolists.components.html'),
                            ])
                            ->id('content'),
                        Tab::make('Properties')
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
                                    ->state(fn (ResourceHubArticle $record): int => $record->views()->count()),
                            ])
                            ->id('properties')
                            ->columns(2),
                        Tab::make('Metadata')
                            ->schema([
                                TextEntry::make('status.name')
                                    ->label('Status'),
                                TextEntry::make('quality.name')
                                    ->label('Quality'),
                                TextEntry::make('category.name')
                                    ->label('Category'),
                                TextEntry::make('division.name')
                                    ->label('Division'),
                                TextEntry::make('managers')
                                    ->label('Managers')
                                    ->getStateUsing(fn (ResourceHubArticle $record) => $record->managers->pluck('name')->join(', ')),
                            ])
                            ->id('metadata'),
                        Tab::make('Concerns')
                            ->schema([
                                Livewire::make(ResourceHubArticleConcernsTable::class, ['record' => $this->getRecord()]),
                            ])
                            ->id('concerns'),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $resourceHubArticle = $this->getRecord();

        return [
            SendEmailAction::make('resource-hub::components.default-email-body')
                ->label('Email Details'),
            Action::make('upvote')
                ->label(fn (): string => ($resourceHubArticle->isUpvoted() ? 'Upvoted ' : 'Upvote ') . "({$resourceHubArticle->upvotes()->count()})")
                ->color(fn (): string => $resourceHubArticle->isUpvoted() ? 'success' : 'gray')
                ->icon('heroicon-m-hand-thumb-up')
                ->action(fn () => $resourceHubArticle->toggleUpvote()),
            CreateConcernAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
