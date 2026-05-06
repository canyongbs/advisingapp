<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Filament\Actions;

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Models\EngagementResponse;
use App\Features\EngagementResponseMarkAsActionedFeature;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkChangeStatusAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulkChangeStatus')
            ->label('Update Status')
            ->icon('heroicon-s-check-circle')
            ->modalHeading('Bulk assign message status')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} " . Str::plural('message', $records->count()) . ' that this change will impact.')
            ->schema([
                Select::make('status')
                    ->label('Which status should be applied?')
                    ->required()
                    ->live(condition: EngagementResponseMarkAsActionedFeature::active())
                    ->options(EngagementResponseStatus::class),
                Textarea::make('note')
                    ->label('Note')
                    ->helperText('Please describe the steps you have taken.')
                    ->rows(4)
                    ->visible(fn (Get $get): bool => EngagementResponseMarkAsActionedFeature::active() && $get('status') === EngagementResponseStatus::Actioned)
                    ->required(fn (Get $get): bool => EngagementResponseMarkAsActionedFeature::active() && $get('status') === EngagementResponseStatus::Actioned),
            ])
            ->action(function (Collection $records, array $data): void {
                $isActioned = $data['status'] === EngagementResponseStatus::Actioned;

                DB::transaction(static function () use ($records, $data, $isActioned): void {
                    $records->each(static function (EngagementResponse $record) use ($data, $isActioned): void {
                        if (EngagementResponseMarkAsActionedFeature::active()) {
                            if ($isActioned) {
                                $record->actionedNotes()->create([
                                    'note' => $data['note'],
                                ]);
                                $record->update(['status' => EngagementResponseStatus::Actioned]);
                            } else {
                                $record->update(['status' => EngagementResponseStatus::New]);
                            }
                        } else {
                            $record->status = $data['status'];
                            $record->save();
                        }
                    });
                });
            });
    }
}
