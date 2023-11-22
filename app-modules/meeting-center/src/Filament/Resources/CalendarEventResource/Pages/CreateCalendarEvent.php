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

namespace Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DateTimePicker;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource;

class CreateCalendarEvent extends CreateRecord
{
    protected static string $resource = CalendarEventResource::class;

    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();

        return $form->schema([
            TextInput::make('title')
                ->string()
                ->required(),
            Textarea::make('description')
                ->string()
                ->nullable(),
            DateTimePicker::make('starts_at')
                ->timezone($user->timezone)
                ->required(),
            DateTimePicker::make('ends_at')
                ->timezone($user->timezone)
                ->required(),
            TagsInput::make('attendees')
                ->placeholder('Add attendee email')
                ->default([$user->calendar->provider_email])
                ->nestedRecursiveRules(['email']),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User $user */
        $user = auth()->user();

        $data = parent::mutateFormDataBeforeCreate($data);
        $data['calendar_id'] = $user->calendar->id;

        return $data;
    }
}
