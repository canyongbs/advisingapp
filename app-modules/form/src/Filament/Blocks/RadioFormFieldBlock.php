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
use Filament\Forms\Components\KeyValue;

class RadioFormFieldBlock extends FormFieldBlock
{
    public string $preview = 'form::blocks.previews.radio';

    public string $rendered = 'form::blocks.submissions.radio';

    public ?string $icon = 'heroicon-m-list-bullet';

    public static function type(): string
    {
        return 'radio';
    }

    public function fields(): array
    {
        return [
            KeyValue::make('options')
                ->keyLabel('Value')
                ->valueLabel('Label'),
        ];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'radio',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
            'options' => $field->config['options'],
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return [
            'string',
            'in:' . collect($field->config['options'])->keys()->join(','),
        ];
    }
}
