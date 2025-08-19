<?php

namespace AdvisingApp\Form\Filament\Blocks;

use AdvisingApp\Form\Models\SubmissibleField;

class UploadFormFieldBlock extends FormFieldBlock
{
    public ?string $icon = 'heroicon-m-document-arrow-up';

    //Don't use in filament
    public static bool $internal = true;

    public static function type(): string
    {
        return 'upload';
    }

    public function fields(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'upload',
            'label' => $field->label,
            'name' => $field->getKey(),
            ...($field->is_required ? ['validation' => 'required'] : []),
            'multiple' => $field->config['multiple'] ?? false,
            'accept' => $field->config['accept'] ?? '',
            'limit' => $field->config['limit'] ?? null,
            'size' => $field->config['size'] ?? null,
            'uploadUrl' => route('api.portal.service-request.request-upload-url'),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }
}
