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

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use Filament\Forms\Form;
use Assist\Division\Models\Division;
use App\Forms\Components\ColorSelect;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MorphToSelect::make('relatedTo')
                    ->label('Related To')
                    ->types([
                        Type::make(Division::class)
                            ->titleAttribute('name'),
                    ])
                    ->required(),
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                ColorSelect::make('primary_color'),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3')
                    ->collection('logo')
                    ->visibility('private')
                    ->image(),
            ]);
    }
}
