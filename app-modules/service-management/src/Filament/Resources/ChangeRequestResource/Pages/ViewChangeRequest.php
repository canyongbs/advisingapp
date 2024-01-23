<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

class ViewChangeRequest extends ViewRecord
{
    protected static string $resource = ChangeRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Change Request Details')
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpan(3),
                        TextEntry::make('description')
                            ->columnSpan(3),
                        TextEntry::make('type.name')
                            ->label('Type')
                            ->columnSpan(3),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->columnSpan(3),
                        TextEntry::make('reason')
                            ->label('Reason for change')
                            ->columnSpanFull(),
                        TextEntry::make('backout_strategy')
                            ->columnSpanFull(),
                        TextEntry::make('start_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('end_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('created_at')
                            ->label('Duration')
                            ->state(fn ($record) => $record->end_time->diffForHumans($record->start_time, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                            ->columnSpan(2),
                    ])
                    ->columns(6),
                Section::make('Risk Management')
                    ->schema([
                        TextEntry::make('impact')
                            ->columnSpan(1),
                        TextEntry::make('likelihood')
                            ->columnSpan(1),
                        ViewEntry::make('risk_score')
                            ->view('filament.infolists.entries.change-request.risk-score')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approveChangeRequest')
                ->requiresConfirmation()
                ->disabled(fn ($record) => ! $record->canBeApprovedBy(auth()->user()))
                ->action(fn ($record) => $record->approvedBy(auth()->user())),
        ];
    }
}
