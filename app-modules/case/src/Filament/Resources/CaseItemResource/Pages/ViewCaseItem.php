<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Assist\Case\Models\CaseItem;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use Filament\Resources\Pages\ViewRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\CaseItemResource;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class ViewCaseItem extends ViewRecord
{
    protected static string $resource = CaseItemResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('casenumber')
                    ->label('Case Number')
                    ->translateLabel(),
                TextEntry::make('institution.name')
                    ->label('Institution')
                    ->translateLabel(),
                TextEntry::make('status.name')
                    ->label('Status')
                    ->translateLabel(),
                TextEntry::make('priority.name')
                    ->label('Priority')
                    ->translateLabel(),
                TextEntry::make('type.name')
                    ->label('Type')
                    ->translateLabel(),
                TextEntry::make('close_details')
                    ->label('Close Details/Description')
                    ->translateLabel()
                    ->columnSpanFull(),
                TextEntry::make('res_details')
                    ->label('Internal Case Details')
                    ->translateLabel()
                    ->columnSpanFull(),
                TextEntry::make('respondent')
                    ->label('Respondent')
                    ->translateLabel()
                    ->color('primary')
                    ->state(function (CaseItem $record): string {
                        /** @var Student|Prospect $respondent */
                        $respondent = $record->respondent;

                        return match ($respondent::class) {
                            Student::class => "{$respondent->full} (Student)",
                            Prospect::class => "{$respondent->full} (Prospect)",
                        };
                    })
                    ->url(function (CaseItem $record) {
                        /** @var Student|Prospect $respondent */
                        $respondent = $record->respondent;

                        return match ($respondent::class) {
                            Student::class => StudentResource::getUrl('view', ['record' => $respondent->sisid]),
                            Prospect::class => ProspectResource::getUrl('view', ['record' => $respondent->id]),
                        };
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
