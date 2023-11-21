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

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\Checkbox;
use Assist\Form\Actions\ResolveSubmissionAuthorFromEmail;
use Filament\Forms\Components\TextInput as FilamentTextInput;

class EducatableEmailFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Student email address';

    public string $rendered = 'form::blocks.submissions.educatable-email';

    public ?string $icon = 'heroicon-m-user';

    public static function type(): string
    {
        return 'educatable_email';
    }

    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Your email address'),
            Checkbox::make('isRequired')
                ->label('Required')
                ->default(true),
        ];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'email',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'email', 'max:255'];
    }

    public static function getSubmissionState(mixed $response): array
    {
        $author = app(ResolveSubmissionAuthorFromEmail::class)($response);

        return [
            'response' => $response,
            'authorKey' => $author ? $author->getKey() : null,
            'authorType' => $author ? $author::class : null,
        ];
    }
}
