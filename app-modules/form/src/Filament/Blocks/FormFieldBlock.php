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
use FilamentTiptapEditor\TiptapBlock;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput as FilamentTextInput;

abstract class FormFieldBlock extends TiptapBlock
{
    public string $preview = 'form::blocks.previews.default';

    public string $rendered = 'form::blocks.submissions.default';

    public ?string $icon = 'heroicon-m-cube';

    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            Checkbox::make('isRequired')
                ->label('Required'),
            ...$this->fields(),
        ];
    }

    public function getLabel(): string
    {
        return $this->label ?? (string) str(static::type())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getIdentifier(): string
    {
        return static::type();
    }

    public function fields(): array
    {
        return [];
    }

    abstract public static function type(): string;

    abstract public static function getFormKitSchema(FormField $field): array;

    public static function getValidationRules(FormField $field): array
    {
        return [];
    }

    public static function getSubmissionState(mixed $response): array
    {
        return [
            'response' => $response,
        ];
    }
}
