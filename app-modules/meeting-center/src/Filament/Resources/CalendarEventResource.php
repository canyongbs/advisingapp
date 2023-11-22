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

namespace Assist\MeetingCenter\Filament\Resources;

use Filament\Resources\Resource;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\EditCalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ViewCalendarEvent;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ListCalendarEvents;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\CreateCalendarEvent;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Meeting Center';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Schedule';

    protected static ?string $modelLabel = 'Event';

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalendarEvents::route('/'),
            'create' => CreateCalendarEvent::route('/create'),
            'view' => ViewCalendarEvent::route('/{record}'),
            'edit' => EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
