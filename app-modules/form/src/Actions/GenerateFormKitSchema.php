<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Actions;

use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleStep;
use Illuminate\Database\Eloquent\Collection;

class GenerateFormKitSchema
{
    public function __invoke(Submissible $submissible): array
    {
        return [
            '$cmp' => 'FormKit',
            'props' => [
                'type' => 'form',
                'id' => 'form',
                'onSubmit' => '$submitForm',
                'plugins' => '$plugins',
                'actions' => false,
            ],
            'children' => $this->generateContent($submissible),
        ];
    }

    public function content(array $blocks, array $content, ?Collection $fields = null): array
    {
        return array_map(
            fn (array $component): array | string => match ($component['type'] ?? null) {
                'bulletList' => ['$el' => 'ul', 'children' => $this->content($blocks, $component['content'] ?? [], $fields)],
                'grid' => $this->grid($blocks, $component, $fields),
                'gridColumn' => ['$el' => 'div', 'children' => $this->content($blocks, $component['content'], $fields), 'attrs' => ['class' => ['grid-col' => true]]],
                'heading' => ['$el' => "h{$component['attrs']['level']}", 'children' => $this->content($blocks, $component['content'], $fields)],
                'horizontalRule' => ['$el' => 'hr'],
                'listItem' => ['$el' => 'li', 'children' => $this->content($blocks, $component['content'] ?? [], $fields)],
                'orderedList' => ['$el' => 'ol', 'children' => $this->content($blocks, $component['content'] ?? [], $fields)],
                'paragraph' => ['$el' => 'p', 'children' => $this->content($blocks, $component['content'] ?? [], $fields)],
                'small' => ['$el' => 'small', 'children' => $this->content($blocks, $component['content'] ?? [], $fields)],
                'text' => $this->text($component),
                'tiptapBlock' => ($field = ($fields[$component['attrs']['id']] ?? null)) ? $blocks[$component['attrs']['type']]::getFormKitSchema($field) : [],
                default => [],
            },
            $content,
        );
    }

    public function grid(array $blocks, array $component, ?Collection $fields): array
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
            'children' => $this->content($blocks, $component['content'], $fields),
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

    public function wizardContent(array $blocks, Submissible $submissible): array
    {
        return [
            [
                '$el' => 'ul',
                'attrs' => [
                    'class' => 'wizard',
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
                    ...$submissible->steps->map(fn (SubmissibleStep $step): array => [
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
                                'children' => $this->content($blocks, $step->content['content'] ?? [], $step->fields->keyBy('id')),
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
                                'disabled' => '$activeStep === "' . $submissible->steps->first()->label . '"',
                                'onClick' => '$setStep(-1)',
                                'children' => 'Previous Step',
                            ],
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'style' => [
                                        'if' => '$activeStep === "' . $submissible->steps->last()->label . '"',
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
                                        'if' => '$activeStep !== "' . $submissible->steps->last()->label . '"',
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

    protected function generateContent(Submissible $submissible): array
    {
        $blocks = app(ResolveBlockRegistry::class)($submissible);

        if ($submissible->is_wizard) {
            $submissible->loadMissing([
                'steps' => [
                    'fields',
                ],
            ]);

            $content = $this->wizardContent($blocks, $submissible);
        } else {
            $submissible->loadMissing([
                'fields',
            ]);

            $content = [
                ...$this->content($blocks, $submissible->content['content'] ?? [], $submissible->fields->keyBy('id')),
                [
                    '$formkit' => 'submit',
                    'label' => 'Submit',
                    'disabled' => '$get(form).state.valid !== true',
                ],
            ];
        }

        return $content;
    }
}
