<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Prospect\Models\Prospect;
use Filament\Resources\Pages\ViewRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('service_request_number')
                            ->label('Service Request Number')
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
                            ->label('Internal Service Request Details')
                            ->translateLabel()
                            ->columnSpanFull(),
                        TextEntry::make('respondent')
                            ->label('Respondent')
                            ->translateLabel()
                            ->color('primary')
                            ->state(function (ServiceRequest $record): string {
                                /** @var Student|Prospect $respondent */
                                $respondent = $record->respondent;

                                return match ($respondent::class) {
                                    Student::class => "{$respondent->{Student::displayNameKey()}} (Student)",
                                    Prospect::class => "{$respondent->{Prospect::displayNameKey()}} (Prospect)",
                                };
                            })
                            ->url(function (ServiceRequest $record) {
                                /** @var Student|Prospect $respondent */
                                $respondent = $record->respondent;

                                return match ($respondent::class) {
                                    Student::class => StudentResource::getUrl('view', ['record' => $respondent->sisid]),
                                    Prospect::class => ProspectResource::getUrl('view', ['record' => $respondent->id]),
                                };
                            }),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
