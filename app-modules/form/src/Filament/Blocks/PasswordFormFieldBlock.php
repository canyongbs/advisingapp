<?php

namespace AdvisingApp\Form\Filament\Blocks;

use AdvisingApp\Form\Models\Secret;
use AdvisingApp\Form\Models\Submissible;
use AdvisingApp\Form\Models\SubmissibleField;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Validation\Rule;

class PasswordFormFieldBlock extends FormFieldBlock
{
    public static function type(): string
    {
        return 'password';
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field, ?Submissible $submissible = null, Student|Prospect|null $author = null): array
    {
        return [
            '$formkit' => 'password',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'storeUrl' => route('widgets.forms.store-secret'),
            ...self::getDescriptionSectionsSchema($field),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return [
            'nullable',
            'uuid',
            Rule::exists(Secret::class, 'id')->whereNull('related_id'),
        ];
    }

    protected static function renderedView(): string
    {
        return 'form::blocks.submissions.password';
    }
}
