<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\KnowledgeBase\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ViewKnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ListKnowledgeBaseItems;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem;

class KnowledgeBaseItemResource extends Resource
{
    protected static ?string $model = KnowledgeBaseItem::class;

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'question';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['question', 'solution'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['quality', 'status', 'category', 'division']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Quality' => $record->quality?->name,
            'Status' => $record->status?->name,
            'Category' => $record->category?->name,
            'Division' => $record->division->pluck('name')->implode(', '),
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeBaseItems::route('/'),
            'create' => CreateKnowledgeBaseItem::route('/create'),
            'view' => ViewKnowledgeBaseItem::route('/{record}'),
            'edit' => EditKnowledgeBaseItem::route('/{record}/edit'),
        ];
    }
}
