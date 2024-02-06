<?php

namespace AdvisingApp\Notification\Filament\Actions;

use App\Models\User;
use Closure;
use Filament\Actions\ViewAction;
use App\Filament\Resources\UserResource;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Infolists\Components\KeyValueEntry;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use Illuminate\Contracts\Support\Htmlable;

class OutboundDeliverableViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        // ray($this->record);

        $this->infolist([
            TextEntry::make('recipient')
                ->getStateUsing(fn (OutboundDeliverable $record): ?string => $record->recipient?->{$record->recipient::displayNameKey()})
                ->url(fn (OutboundDeliverable $record) => match ($record->recipient ? $record->recipient::class : null) {
                    Student::class => StudentResource::getUrl('view', ['record' => $record->recipient]),
                    Prospect::class => ProspectResource::getUrl('view', ['record' => $record->recipient]),
                    User::class => UserResource::getUrl('view', ['record' => $record->recipient]),
                    default => null,
                })
                ->color('primary'),
            TextEntry::make('channel'),
            TextEntry::make('delivery_status'),
            // KeyValueEntry::make('content'),
            // KeyValueEntry::make('content')
            //     ->state(function (OutboundDeliverable $record) {
            //         return json_decode($record->content);
            //     }),
        ]);
    }
}
