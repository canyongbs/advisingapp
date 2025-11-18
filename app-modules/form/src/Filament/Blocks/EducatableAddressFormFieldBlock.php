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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Form\Filament\Blocks;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput as FilamentTextInput;

class EducatableAddressFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Primary Address';

    public string $preview = 'form::blocks.previews.educatable-address';

    public string $rendered = 'form::blocks.submissions.educatable-address';

    public ?string $icon = 'heroicon-m-map-pin';

    public static function type(): string
    {
        return 'educatable_address';
    }

    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Address'),
            Checkbox::make('isRequired')
                ->label('Required')
                ->default(false),
        ];
    }

    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        $baseKey = $field->getKey();
        $helpText = null;
        $disabled = false;

        if ($author && $submissible && in_array($submissible::class, [Form::class, Application::class])) {
            if ($author instanceof Student) {
                $disabled = true;
                $helpText = 'This data is synchronized from your college\'s student information system. To update this data, please update your information in the source system and wait 24 hours for it to be reflected here.';
            } elseif ($author instanceof Prospect) {
                $helpText = 'This field has been pre-populated with the information we have on file. Please feel free to update it and we will update our records accordingly.';
            }
        }

        $address = $author?->primaryAddress;

        return [
            '$formkit' => 'group',
            'name' => $baseKey,
            'label' => $field->label,
            ...($helpText ? ['help' => $helpText] : []),
            'children' => [
                [
                    '$formkit' => 'text',
                    'name' => 'line_1',
                    'label' => 'Address Line 1',
                    'value' => $address?->line_1 ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                    ...($field->is_required ? ['validation' => 'required'] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'line_2',
                    'label' => 'Address Line 2',
                    'value' => $address?->line_2 ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'line_3',
                    'label' => 'Address Line 3',
                    'value' => $address?->line_3 ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'city',
                    'label' => 'City',
                    'value' => $address?->city ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                    ...($field->is_required ? ['validation' => 'required'] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'state',
                    'label' => 'State',
                    'value' => $address?->state ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'postal',
                    'label' => 'Postal Code',
                    'value' => $address?->postal ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'country',
                    'label' => 'Country',
                    'value' => $address?->country ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                ],
            ],
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return ['array', 'nullable'];
    }

    public static function getNestedValidationRules(SubmissibleField $field): array
    {
        $nestedRules = [];

        if ($field->is_required) {
            $nestedRules['line_1'] = ['required', 'string', 'max:255'];
            $nestedRules['city'] = ['required', 'string', 'max:255'];
        } else {
            $nestedRules['line_1'] = ['nullable', 'string', 'max:255'];
            $nestedRules['city'] = ['nullable', 'string', 'max:255'];
        }

        $nestedRules['line_2'] = ['nullable', 'string', 'max:255'];
        $nestedRules['line_3'] = ['nullable', 'string', 'max:255'];
        $nestedRules['state'] = ['nullable', 'string', 'max:255'];
        $nestedRules['postal'] = ['nullable', 'string', 'max:255'];
        $nestedRules['country'] = ['nullable', 'string', 'max:255'];

        return $nestedRules;
    }
}
