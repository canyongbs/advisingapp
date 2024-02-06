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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Carbon\CarbonInterval;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Group;
use Filament\Resources\Pages\ViewRecord;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;

class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $formatSecondsAsInterval = fn (?int $state): ?string => $state ? CarbonInterval::seconds($state)->cascade()->forHumans(short: true) : null;

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('service_request_number')
                            ->label('Service Request Number')
                            ->translateLabel(),
                        TextEntry::make('division.name')
                            ->label('Division')
                            ->translateLabel(),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->translateLabel(),
                        TextEntry::make('priority.name')
                            ->label('Priority')
                            ->translateLabel(),
                        TextEntry::make('priority.type.name')
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
                            ->label('Related To')
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
                    ->columns(2),
                Section::make('SLA Management')
                    ->visible(fn (ServiceRequest $record): bool => $record->priority?->sla !== null)
                    ->schema([
                        Group::make([
                            TextEntry::make('sla_response_seconds')
                                ->label('Response agreement')
                                ->state(fn (ServiceRequest $record): ?int => $record->getSlaResponseSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('response_age')
                                ->label('Response age')
                                ->state(fn (ServiceRequest $record): ?int => $record->getLatestResponseSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('response_sla_compliance')
                                ->label('Response compliance')
                                ->badge()
                                ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResponseSlaComplianceStatus()),
                        ]),
                        Group::make([
                            TextEntry::make('sla_resolution_seconds')
                                ->label('Resolution agreement')
                                ->state(fn (ServiceRequest $record): ?int => $record->getSlaResolutionSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('resolution_seconds')
                                ->label('Resolution age')
                                ->state(fn (ServiceRequest $record): int => $record->getResolutionSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('resolution_sla_compliance')
                                ->label('Resolution compliance')
                                ->badge()
                                ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResolutionSlaComplianceStatus()),
                        ]),
                    ])
                    ->columns(2),
                Section::make('Form Submission Details')
                    ->collapsed()
                    ->visible(fn (ServiceRequest $record): bool => ! is_null($record->serviceRequestFormSubmission))
                    ->schema([
                        TextEntry::make('serviceRequestFormSubmission.submitted_at')
                            ->dateTime(),
                        ViewEntry::make('serviceRequestFormSubmission')
                            ->view('filament.infolists.entries.submission-entry'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
