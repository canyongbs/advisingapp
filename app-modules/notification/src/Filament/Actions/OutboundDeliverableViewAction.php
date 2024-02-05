<?php

namespace AdvisingApp\Notification\Filament\Actions;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AdvisingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\View;

class OutboundDeliverableViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

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
        ]);
    }
}
