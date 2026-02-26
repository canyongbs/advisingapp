<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events;

use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\CreateEvent;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\EditEventDetails;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\EditEventPage;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\EditEventRegistration;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ListEvents;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ManageEventAttendees;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ViewEvent;
use AdvisingApp\MeetingCenter\Models\Event;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string | UnitEnum | null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $breadcrumb = 'Events';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewEvent::class,
            EditEventDetails::class,
            EditEventPage::class,
            EditEventRegistration::class,
            ManageEventAttendees::class,
        ]);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return EventResource::getUrl('view', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit-details' => EditEventDetails::route('/{record}/edit-details'),
            'edit-page' => EditEventPage::route('/{record}/edit-page'),
            'edit-registration' => EditEventRegistration::route('/{record}/edit-registration'),
            'manage-attendees' => ManageEventAttendees::route('/{record}/manage-attendees'),
        ];
    }
}
