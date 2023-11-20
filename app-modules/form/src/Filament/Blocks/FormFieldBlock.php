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
use Filament\Infolists\Components\Entry;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput as FilamentTextInput;

abstract class FormFieldBlock extends Block
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255),
            FilamentTextInput::make('key')
                ->required()
                ->string()
                ->maxLength(255),
            Checkbox::make('required'),
            ...$this->fields(),
        ]);
    }

    public static function make(string $name = null): static
    {
        return parent::make($name ?? static::type());
    }

    abstract public function fields(): array;

    abstract public static function type(): string;

    abstract public static function getInfolistEntry(FormField $field): Entry;

    abstract public static function getFormKitSchema(FormField $field): array;

    public static function getValidationRules(FormField $field): array
    {
        return [];
    }
}
