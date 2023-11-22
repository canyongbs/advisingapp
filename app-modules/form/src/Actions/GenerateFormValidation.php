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

namespace Assist\Form\Actions;

use Illuminate\Support\Arr;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Collection;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;

class GenerateFormValidation
{
    public function __invoke(Form $form): array
    {
        if ($form->is_wizard) {
            return $this->wizardRules($form);
        }

        return $this->fields($form->fields);
    }

    public function fields(Collection $fields): array
    {
        $blocks = FormFieldBlockRegistry::keyByType();

        return $fields
            ->mapWithKeys(function (FormField $field) use ($blocks) {
                $rules = collect();

                if ($field->is_required) {
                    $rules->push('required');
                }

                return [$field->id => $rules
                    ->merge($blocks[$field->type]::getValidationRules($field))
                    ->all()];
            })
            ->all();
    }

    public function wizardRules(Form $form): array
    {
        $rules = collect();

        foreach ($form->steps as $step) {
            $rules = $rules->merge(
                Arr::prependKeysWith(
                    $this->fields($step->fields),
                    prependWith: "{$step->label}.",
                ),
            );
        }

        return $rules->all();
    }
}
