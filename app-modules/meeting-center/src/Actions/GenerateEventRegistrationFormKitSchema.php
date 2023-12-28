<?php

namespace AdvisingApp\MeetingCenter\Actions;

use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Actions\GenerateFormKitSchema;

class GenerateEventRegistrationFormKitSchema extends GenerateFormKitSchema
{
    public function __invoke(Submissible $submissible): array
    {
        $content = $this->generateContent($submissible);

        $content = [
            [
                '$formkit' => 'radio',
                'label' => 'Will you be attending?',
                'id' => 'attending',
                'name' => 'attending',
                'options' => [
                    [
                        'label' => 'Yes',
                        'value' => 'yes',
                    ],
                    [
                        'label' => 'No',
                        'value' => 'no',
                    ],
                ],
                'validation' => 'required',
            ],
            [
                '$el' => 'div',
                'if' => '$get(attending).value === "yes"',
                'children' => $content,
            ],
            [
                '$formkit' => 'submit',
                'if' => '$get(attending).value === "no"',
                'label' => 'Submit',
                'disabled' => '$get(form).state.valid !== true',
            ],
        ];

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
}