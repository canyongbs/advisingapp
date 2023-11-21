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

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('question')
                            ->label('Question/Issue/Feature')
                            ->translateLabel(),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->translateLabel(),
                        TextEntry::make('quality.name')
                            ->label('Quality')
                            ->translateLabel(),
                        TextEntry::make('category.name')
                            ->label('Category')
                            ->translateLabel(),
                        TextEntry::make('public')
                            ->label('Public')
                            ->translateLabel()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('division.name')
                            ->label('Division')
                            ->translateLabel(),
                        ViewEntry::make('solution')
                            ->label('Solution')
                            ->translateLabel()
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                        ViewEntry::make('notes')
                            ->label('Notes')
                            ->translateLabel()
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
