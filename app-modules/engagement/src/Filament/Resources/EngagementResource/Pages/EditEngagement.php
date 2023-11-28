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

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Filament\Resources\EngagementResource;

class EditEngagement extends EditRecord
{
    protected static string $resource = EngagementResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                TextInput::make('subject')
                    ->autofocus()
                    ->required()
                    ->placeholder(__('Subject')),
                Textarea::make('body')
                    ->autofocus()
                    ->placeholder(__('Body'))
                    ->columnSpanFull(),
                MorphToSelect::make('recipient')
                    ->label('Recipient')
                    ->searchable()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
                Fieldset::make('Send your engagement')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (callable $get) => $get('send_later')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
