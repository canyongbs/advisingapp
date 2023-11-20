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

use Assist\Form\Models\Form;
use Assist\Form\Models\FormStep;
use Assist\Form\Models\FormField;
use Illuminate\Database\Eloquent\Collection;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;

class GenerateFormKitSchema
{
    public function __invoke(Form $form): array
    {
        if ($form->is_wizard) {
            $content = $this->wizardContent($form);
        } else {
            $content = [
                ...$this->fields($form->fields),
                [
                    '$formkit' => 'submit',
                    'label' => 'Submit',
                    'disabled' => '$get(form).state.valid !== true',
                ],
            ];
        }

        return [
            '$cmp' => 'FormKit',
            'props' => [
                'type' => 'form',
                'id' => 'form',
                'onSubmit' => '$submitForm',
                'plugins' => '$plugins',
                'actions' => false,
            ],
            'children' => $content,
        ];
    }

    public function fields(Collection $fields): array
    {
        $blocks = FormFieldBlockRegistry::keyByType();

        return $fields
            ->map(fn (FormField $field): array => $blocks[$field->type]::getFormKitSchema($field))
            ->all();
    }

    public function wizardContent(Form $form): array
    {
        return [
            [
                '$el' => 'ul',
                'attrs' => [
                    'class' => 'steps',
                ],
                'children' => [
                    [
                        '$el' => 'li',
                        'for' => [
                            'step',
                            'stepName',
                            '$steps',
                        ],
                        'attrs' => [
                            'class' => [
                                'step' => true,
                                'has-errors' => '$showStepErrors($stepName)',
                            ],
                            'onClick' => '$setActiveStep($stepName)',
                            'data-step-active' => '$activeStep === $stepName',
                            'data-step-valid' => '$stepIsValid($stepName)',
                        ],
                        'children' => [
                            [
                                '$el' => 'span',
                                'if' => '$showStepErrors($stepName)',
                                'attrs' => [
                                    'class' => 'step--errors',
                                ],
                                'children' => '$step.errorCount + $step.blockingCount',
                            ],
                            '$stepName',
                        ],
                    ],
                ],
            ],
            [
                '$el' => 'div',
                'attrs' => [
                    'class' => 'form-body',
                ],
                'children' => [
                    ...$form->steps->map(fn (FormStep $step): array => [
                        '$el' => 'section',
                        'attrs' => [
                            'style' => [
                                'if' => '$activeStep !== "' . $step->label . '"',
                                'then' => 'display: none;',
                            ],
                        ],
                        'children' => [
                            [
                                '$formkit' => 'group',
                                'id' => $step->label,
                                'name' => $step->label,
                                'children' => $this->fields($step->fields),
                            ],
                        ],
                    ]),
                    [
                        '$el' => 'div',
                        'attrs' => [
                            'class' => 'step-nav',
                        ],
                        'children' => [
                            [
                                '$formkit' => 'button',
                                'disabled' => '$activeStep === "' . $form->steps->first()->label . '"',
                                'onClick' => '$setStep(-1)',
                                'children' => 'Previous Step',
                            ],
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'style' => [
                                        'if' => '$activeStep === "' . $form->steps->last()->label . '"',
                                        'then' => 'display: none;',
                                    ],
                                ],
                                'children' => [
                                    [
                                        '$formkit' => 'button',
                                        'onClick' => '$setStep(1)',
                                        'children' => 'Next Step',
                                    ],
                                ],
                            ],
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'style' => [
                                        'if' => '$activeStep !== "' . $form->steps->last()->label . '"',
                                        'then' => 'display: none;',
                                    ],
                                ],
                                'children' => [
                                    [
                                        '$formkit' => 'submit',
                                        'label' => 'Submit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
