<?php

namespace Assist\Form\Actions;

use Assist\Form\Models\Form;
use Assist\Form\Models\FormStep;
use Assist\Form\Models\FormField;

class GenerateFormKitSchema
{
    public function __invoke(Form $form): array
    {
        if ($form->is_wizard) {
            $content = $this->wizardContent($form);
        } else {
            $content = [
                ...$form->fields->map(fn (FormField $field): array => $this->field($field)),
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

    public function field(FormField $formField): array
    {
        return match ($formField->type) {
            'text_input' => [
                '$formkit' => 'text',
                'label' => $formField->label,
                'name' => $formField->key,
                ...($formField->required ? ['validation' => 'required'] : []),
            ],
            'text_area' => [
                '$formkit' => 'textarea',
                'label' => $formField->label,
                'name' => $formField->key,
                ...($formField->required ? ['validation' => 'required'] : []),
            ],
            'select' => [
                '$formkit' => 'select',
                'label' => $formField['label'],
                'name' => $formField->key,
                ...($formField->required ? ['validation' => 'required'] : []),
                'options' => $formField->config['options'],
            ],
            'signature' => [
                '$formkit' => 'signature',
                'label' => $formField->label,
                'name' => $formField->key,
                ...($formField->required ? ['validation' => 'required'] : []),
            ],
        };
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
                                'children' => $step->fields->map(fn (FormField $field): array => $this->field($field)),
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
