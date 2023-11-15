<?php

namespace Assist\Form\Filament\Blocks;

use Assist\Form\Models\FormField;
use Filament\Forms\Components\Checkbox;
use Assist\Form\Actions\ResolveSubmissionAuthorFromEmail;
use Filament\Forms\Components\TextInput as FilamentTextInput;

class EducatableEmailFormFieldBlock extends FormFieldBlock
{
    public ?string $label = 'Student email address';

    public string $rendered = 'form::blocks.submissions.educatable-email';

    public static function type(): string
    {
        return 'educatable_email';
    }

    public function getFormSchema(): array
    {
        return [
            FilamentTextInput::make('label')
                ->required()
                ->string()
                ->maxLength(255)
                ->default('Your email address'),
            Checkbox::make('isRequired')
                ->label('Required')
                ->default(true),
        ];
    }

    public static function getFormKitSchema(FormField $field): array
    {
        return [
            '$formkit' => 'email',
            'label' => $field->label,
            'name' => $field->id,
            ...($field->is_required ? ['validation' => 'required'] : []),
        ];
    }

    public static function getValidationRules(FormField $field): array
    {
        return ['string', 'email', 'max:255'];
    }

    public static function getSubmissionState(mixed $response): array
    {
        $author = app(ResolveSubmissionAuthorFromEmail::class)($response);

        return [
            'response' => $response,
            'authorKey' => $author ? $author->getKey() : null,
            'authorType' => $author ? $author::class : null,
        ];
    }
}
