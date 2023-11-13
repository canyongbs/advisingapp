<?php

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
            $form->loadMissing([
                'steps' => [
                    'fields',
                ],
            ]);

            $content = $this->wizardContent($form);
        } else {
            $form->loadMissing([
                'fields',
            ]);

            $content = [
                ...$this->content($form->content['content'] ?? [], $form->fields->keyBy('id')),
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

    public function content(array $content, ?Collection $fields = null): array
    {
        $blocks = FormFieldBlockRegistry::keyByType();

        return array_map(
            fn (array $component): array | string => match ($component['type'] ?? null) {
                'bulletList' => ['$el' => 'ul', 'children' => $this->content($component['content'] ?? [], $fields)],
                'grid' => $this->grid($component, $fields),
                'gridColumn' => ['$el' => 'div', 'children' => $this->content($component['content'], $fields), 'attrs' => ['class' => ['grid-col' => true]]],
                'heading' => ['$el' => "h{$component['attrs']['level']}", 'children' => $this->content($component['content'], $fields),],
                'horizontalRule' => ['$el' => 'hr'],
                'listItem' => ['$el' => 'li', 'children' => $this->content($component['content'] ?? [], $fields)],
                'orderedList' => ['$el' => 'ol', 'children' => $this->content($component['content'] ?? [], $fields)],
                'paragraph' => ['$el' => 'p', 'children' => $this->content($component['content'], $fields)],
                'small' => ['$el' => 'small', 'children' => $this->content($component['content'] ?? [], $fields)],
                'text' => $this->text($component),
                'tiptapBlock' => $blocks[$component['attrs']['type']]::getFormKitSchema($fields[$component['attrs']['id']]),
                default => [],
            },
            $content,
        );
    }

    public function grid(array $component, ?Collection $fields): array
    {
        return [
            '$el' => 'div',
            'attrs' => [
                'class' => [
                    ...match ($component['attrs']['type']) {
                        'asymetric-left-thirds' => ['asymetric-grid-left-thirds' => true],
                        'asymetric-right-thirds' => ['asymetric-grid-right-thirds' => true],
                        'asymetric-left-fourths' => ['asymetric-grid-left-fourths' => true],
                        'asymetric-right-fourths' => ['asymetric-grid-right-fourths' => true],
                        'fixed' => ['fixed-grid-' . $component['attrs']['cols'] => true],
                        'responsive' => ['responsive-grid-' . $component['attrs']['cols'] => true],
                        default => [],
                    },
                ],
            ],
            'children' => $this->content($component['content'], $fields),
        ];
    }

    public function text(array $component): array | string
    {
        if (filled($component['marks'] ?? [])) {
            return array_reduce(
                $component['marks'],
                fn (array | string $text, array $mark): array | string => match ($mark['type']) {
                    'bold' => [
                        '$el' => 'strong',
                        'children' => $component['text'],
                    ],
                    'italic' => [
                        '$el' => 'em',
                        'children' => $component['text'],
                    ],
                    'link' => [
                        '$el' => 'a',
                        'attrs' => [
                            'href' => $mark['attrs']['href'] ?? null,
                            'target' => $mark['attrs']['target'] ?? null,
                        ],
                        'children' => $component['text'],
                    ],
                    'small' => [
                        '$el' => 'small',
                        'children' => $component['text'],
                    ],
                    default => $text,
                },
                $component['text'],
            );
        }

        return $component['text'];
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
                                'children' => $this->content($step->content['content'] ?? [], $step->fields->keyBy('id')),
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
