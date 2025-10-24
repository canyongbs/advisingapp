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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Ai\Enums\QnaAdvisorReportTableTab;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class QnaAdvisorReportTable extends TableWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Most Recent QnA Advisor Chats';

    #[Url]
    public string $activeTab = QnaAdvisorReportTableTab::Student->value;

    protected string $view = 'ai::filament.resources.qna-advisors.pages.qna-advisor-table-report';

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void {}

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    $startDate = $this->getStartDate();
                    $endDate = $this->getEndDate();

                    $tab = QnaAdvisorReportTableTab::tryFrom($this->activeTab) ?? QnaAdvisorReportTableTab::Student;

                    $qnaAdvisorThreadQuery = fn (Builder $query): Builder => match ($tab) {
                        QnaAdvisorReportTableTab::Student => QnaAdvisorThread::query()
                            ->whereMorphedTo('author', Student::class),
                        QnaAdvisorReportTableTab::Prospect => QnaAdvisorThread::query()
                            ->whereMorphedTo('author', Prospect::class),
                        QnaAdvisorReportTableTab::Unauthenticated => QnaAdvisorThread::query()
                            ->whereNull('author_id'),
                    };

                    return $qnaAdvisorThreadQuery(QnaAdvisorThread::query())
                        ->when(
                            $startDate && $endDate,
                            function (Builder $query) use ($startDate, $endDate): Builder {
                                return $query->whereBetween('created_at', [$startDate, $endDate]);
                            }
                        )
                        ->orderBy('created_at', 'desc')
                        ->take(100);
                }
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('advisor.name')
                    ->label('Name'),
                TextColumn::make('author')
                    ->label('With')
                    ->url(fn (QnaAdvisorThread $record): ?string => match (true) {
                        $record->author instanceof Student => ViewStudent::getUrl(['record' => $record->author]),
                        $record->author instanceof Prospect => ViewProspect::getUrl(['record' => $record->author]),
                        default => null,
                    }, shouldOpenInNewTab: true)
                    ->getStateUsing(function (QnaAdvisorThread $record): string {
                        $author = $record->author;

                        if ($author instanceof Prospect || $author instanceof Student) {
                            return $author->full_name;
                        }

                        return 'N/A';
                    }),
                TextColumn::make('exchanges')
                    ->getStateUsing(fn (QnaAdvisorThread $record) => $record->messages()->where('is_advisor', false)->count()),
                TextColumn::make('finished_at')
                    ->badge()
                    ->color(
                        fn (QnaAdvisorThread $record): string => filled($record->finished_at)
                        && in_array($record->author?->getMorphClass(), ['prospect', 'student']) && filled($record->interaction_id)
                            ? 'info'
                            : 'warning'
                    )
                    ->getStateUsing(
                        fn (QnaAdvisorThread $record) => filled($record->finished_at)
                        && in_array($record->author?->getMorphClass(), ['prospect', 'student']) && filled($record->interaction_id)
                            ? 'Yes'
                            : 'No'
                    )
                    ->label('Interaction Logged'),

                TextColumn::make('interaction_id')
                    ->label('Subscribed Updated')
                    ->badge()
                    ->color(fn (QnaAdvisorThread $record): string => filled($record->interaction_id) ? 'info' : 'warning')
                    ->getStateUsing(fn (QnaAdvisorThread $record) => filled($record->interaction_id) ? 'Yes' : 'No'),
            ])
            ->recordActions([
                Action::make('view_transcript')
                    ->label('View Transcript')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->modalHeading(fn (QnaAdvisorThread $record): string => 'Chat Transcript - ' . $record->advisor->name)
                    ->modalWidth('6xl')
                    ->modalContent(function (QnaAdvisorThread $record) {
                        $messages = $record->messages()
                            ->orderBy('created_at', 'asc')
                            ->get();

                        return view('ai::filament.widgets.qna-advisor-transcript-modal', [
                            'messages' => $messages,
                            'advisor' => $record->advisor,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->paginated([10]);
    }
}
