<?php

namespace AdvisingApp\Notification\Filament\Actions;

use App\Models\User;
use Filament\Actions\ViewAction;
use Illuminate\Support\HtmlString;
use App\Filament\Resources\UserResource;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

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
            TextEntry::make('subject')
                ->getStateUsing(fn (OutboundDeliverable $record): ?string => $record->content['subject']),
            TextEntry::make('body')
                ->getStateUsing(function (OutboundDeliverable $record): ?string {
                    $body = str($record->content['greeting']);

                    foreach ($record->content['introLines'] as $line) {
                        $body = $body->append("<br><br>{$line}");
                    }

                    $body = $record->content['salutation']
                        ? $body->append("<br><br>{$record->content['salutation']}")
                        : $body->append('<br><br>Regards,<br>' . config('app.name'));

                    return new HtmlString($body->trim());
                })
                ->html(),
        ]);
    }
}
