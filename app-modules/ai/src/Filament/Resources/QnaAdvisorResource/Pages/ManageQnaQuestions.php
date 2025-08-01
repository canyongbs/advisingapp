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

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ManageQnaQuestions extends ManageRelatedRecords
{
    protected static string $resource = QnaAdvisorResource::class;

    protected static string $relationship = 'questions';

    protected static ?string $title = 'Questions';

    protected static ?string $navigationGroup = 'Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', modifyQueryUsing: function (Builder $query) {
                        /** @var QnaAdvisor $advisor */
                        $advisor = $this->getOwnerRecord();
                        $query->where('qna_advisor_id', $advisor->getKey());
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                TextInput::make('question')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->required()
                    ->string()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    /**
    * @return array<int|string, string|null>
    */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('question')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('answer')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', modifyQueryUsing: function (Builder $query) {
                        /** @var QnaAdvisor $qnaAdvisor */
                        $qnaAdvisor = $this->getOwnerRecord();
                        $query->where('qna_advisor_id', $qnaAdvisor->getKey());
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Create QnA Advisor Question'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No QnA Advisor Questions Found')
            ->emptyStateDescription('');
    }
}
