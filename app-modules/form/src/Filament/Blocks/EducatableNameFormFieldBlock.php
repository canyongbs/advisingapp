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

class EducatableNameFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Name';

    public string $preview = 'form::blocks.previews.educatable-name';

    public string $rendered = 'form::blocks.submissions.educatable-name';

    public ?string $icon = 'heroicon-m-user';

    public static function type(): string
    {
        return 'educatable_name';
    }

    /**
     * @return array<int, mixed>
     */
    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Name'),
            FilamentTextInput::make('firstNameLabel')
                ->label('First Name Label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('First Name'),
            Checkbox::make('firstNameRequired')
                ->label('First Name Required')
                ->default(true)
                ->disabled(),
            FilamentTextInput::make('lastNameLabel')
                ->label('Last Name Label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Last Name'),
            Checkbox::make('lastNameRequired')
                ->label('Last Name Required')
                ->default(true)
                ->disabled(),
            FilamentTextInput::make('preferredNameLabel')
                ->label('Preferred Name Label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Preferred Name'),
            Checkbox::make('preferredNameRequired')
                ->label('Preferred Name Required')
                ->default(false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        $baseKey = $field->getKey();
        $helpText = null;
        $disabled = false;

        if ($author && $submissible && in_array($submissible::class, [Form::class, Application::class])) {
            if ($author instanceof Student) {
                $disabled = true;
                $helpText = self::MAPPED_STUDENT_FIELD_HELP_TEXT;
            } elseif ($author instanceof Prospect) {
                $helpText = self::MAPPED_PROSPECT_FIELD_HELP_TEXT;
            }
        }

        $firstNameLabel = $field->config['firstNameLabel'] ?? 'First Name';
        $lastNameLabel = $field->config['lastNameLabel'] ?? 'Last Name';
        $preferredNameLabel = $field->config['preferredNameLabel'] ?? 'Preferred Name';
        $preferredNameRequired = $field->config['preferredNameRequired'] ?? false;

        return [
            '$formkit' => 'group',
            'name' => $baseKey,
            'label' => $field->label,
            ...($helpText ? ['help' => $helpText] : []),
            'children' => [
                [
                    '$formkit' => 'text',
                    'name' => 'first_name',
                    'label' => $firstNameLabel,
                    'value' => $author instanceof Student ? ($author->first ?? '') : ($author instanceof Prospect ? ($author->first_name ?? '') : ''),
                    ...($disabled ? ['disabled' => true] : []),
                    'validation' => 'required',
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'last_name',
                    'label' => $lastNameLabel,
                    'value' => $author instanceof Student ? ($author->last ?? '') : ($author instanceof Prospect ? ($author->last_name ?? '') : ''),
                    ...($disabled ? ['disabled' => true] : []),
                    'validation' => 'required',
                ],
                [
                    '$formkit' => 'text',
                    'name' => 'preferred',
                    'label' => $preferredNameLabel,
                    'value' => $author->preferred ?? '',
                    ...($disabled ? ['disabled' => true] : []),
                    ...($preferredNameRequired ? ['validation' => 'required'] : []),
                ],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return ['array', 'nullable'];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function getNestedValidationRules(SubmissibleField $field): array
    {
        $preferredNameRequired = $field->config['preferredNameRequired'] ?? false;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'preferred' => $preferredNameRequired ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'],
        ];
    }
}
